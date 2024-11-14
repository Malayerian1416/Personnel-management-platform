<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CompanyInformation;
use App\Models\ContractConversion;
use App\Models\CustomGroup;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class EmployeeAppointmentLetterController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"EmployeePaySlips");
        try {
            $custom_groups = CustomGroup::query()->whereHas("user",function ($query){
                $query->where("id","=",Auth::id());
            })->get();
            return view("staff.employee_appointment_letter", [
                "organizations" => $this->allowed_contracts("tree"),
                "custom_groups" => $custom_groups,
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function batch_print(Request $request): array
    {
        try {
            $response = [];
            $request->validate([
                "reference" => ["required", Rule::in(['organization', 'group', 'custom','selection'])],
                "contract_id" => ["required_if:reference,organization"],
                "selected_employees" => ["required_if:reference,selection"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"]
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است"
            ]);
            $employees = collect();
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->with(["contract.organization"])->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->with(["contract.organization"])->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->with(["contract.organization"])->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "selection":{
                    $employees_id = json_decode($request->input("selected_employees"));
                    $employees = Employee::query()->with(["contract.organization"])->whereIn("id",$employees_id)->get();
                    break;
                }
            }
            $folder = Str::random(8);
            Storage::disk("temporarily")->makeDirectory($folder);
            $company = CompanyInformation::query()->with("ceo")->first();
            if ($employees->isNotEmpty()) {
                $employees->map(function ($employee) use ($folder,$company){
                    $file = Str::random(8);
                    $number = verta()->format("Ynj").rand(112,999);
                    $pdf = PDF::loadView('layouts.pdf.PAFBatch',[
                        "employee" => $employee,
                        "company_information" => $company,
                        "number" => $number,
                        "sign" => false,
                        "logo" => base64_encode(file_get_contents(public_path("/images/logo.png")))],[], [
                        'format' => "A4-P"
                    ]);
                    $pdf->save(Storage::disk("temporarily")->path("$folder/$file.pdf"));
                });
                $merge = PDFMerger::init();
                foreach (Storage::disk("temporarily")->files($folder) as $file) {
                    $merge->addPDF(Storage::disk("temporarily")->path("$file"), 'all');
                }
                $merge->merge();
                $merge->save(Storage::disk("temporarily")->path("{$folder}.pdf"));
                Storage::disk("temporarily")->deleteDirectory($folder);
                $response["data"]["view"] = route("docs.application_download",["path" => "$folder.pdf"]);
                $response["result"] = "success";
                $response["message"] = "عملیات با موفقیت انجام شد";
                return $response;
            }
            else
                throw ValidationException::withMessages(['employees' => 'پرسنلی جهت انجام عملیات وجود ندارد']);
        }
        catch (Throwable $error) {
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            $response["data"] = [];
            return $response;
        }
    }
}
