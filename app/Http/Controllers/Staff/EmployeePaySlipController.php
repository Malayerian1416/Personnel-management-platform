<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ExportContractPaySlip;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeePaySlipRequest;
use App\Imports\ImportContractPaySlip;
use App\Models\Contract;
use App\Models\EmployeePaySlip;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;
use Illuminate\Support\Facades\Response;

class EmployeePaySlipController extends Controller
{

    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"EmployeePaySlips");
        try {
            return view("staff.employee_payslip", [
                "organizations" => $this->allowed_contracts("tree"),
                "persian_month" => $this->persian_month(),
                "employees" => Session::has("employees") ? Session::get("employees") : [],
                "query" => Session::has("query") ? Session::get("query") : false,
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function query(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                "contract_id" => "required","query_year" => "required", "query_month" => "required"],
                [
                    "contract_id.required" => "انتخاب قرارداد الزامی می باشد",
                    "query_year.required" => "انتخاب سال الزامی می باشد",
                    "query_month.required" => "انتخاب ماه الزامی می باشد",
                ]
            );
            $employees = EmployeePaySlip::BatchPayslip($request->input("contract_id"),$request->input("query_year"),$request->input("query_month"));
            return redirect()->route("EmployeePaySlips.index")->with(["employees" => $employees,"query" => true]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function store(EmployeePaySlipRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"EmployeePaySlips");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $payslips = json_decode($validated["payslip_employees"],true);
            foreach ($payslips as $payslip){
                $date_serial = intval(strlen($validated['month']) == 1 ? "{$validated['year']}0{$validated['month']}" : "{$validated['year']}{$validated['month']}");
                $i_number = verta()->format("Ymd").rand(1123456789,9999999999);
                while (EmployeePaySlip::query()->where("i_number","=",$i_number)->get()->isNotEmpty())
                    $i_number = verta()->format("Ymd").rand(1123456789,9999999999);
                EmployeePaySlip::query()->updateOrCreate(["employee_id" => $payslip["id"],"date_serial" => $date_serial],[
                    "user_id" => Auth::id(),
                    "i_number" => $i_number,
                    "date_serial" => $date_serial,
                    "persian_year" => $validated["year"],
                    "persian_month" => $validated["month"],
                    "persian_month_name" => $this->persian_month($validated["month"]),
                    "contents" => json_encode($payslip["columns"],JSON_UNESCAPED_UNICODE)

                ]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"EmployeePaySlips");
        try {
            $payslip = EmployeePaySlip::query()->with("employee.contract.organization")->findOrFail($id);
            return view("staff.edit_employee_payslip",["payslip" => $payslip]);

        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"EmployeePaySlips");
        try {
            DB::beginTransaction();
            $payslip = EmployeePaySlip::query()->findOrFail($id);
            $payslip->update(["contents" => $request->input("payslip_employees")]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"EmployeePaySlips");
        try {
            DB::beginTransaction();
            $payslip = EmployeePaySlip::query()->findOrFail($id);
            $payslip->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroyAll(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"EmployeePaySlips");
        try {
            DB::beginTransaction();
            EmployeePaySlip::BatchPayslipDelete($request->input("contract_id"),$request->input("delete_query_year"),$request->input("delete_query_month"));
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function excel_download($contract_id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            return Excel::download(new ExportContractPaySlip($contract_id), 'EmployeePaySlip.xlsx');
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function show($id): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        try {
            $payslip = EmployeePaySlip::query()->with("employee.contract.organization")->findOrFail($id);
            $pdf = PDF::loadView('layouts.pdf.payslip',["payslip" => $payslip->published(),"logo" => base64_encode(file_get_contents(public_path("/images/logo.png")))],[], [
                'format' => "A4-L"
            ]);
            return Response::make($pdf->stream("payslip-{$payslip->persian_year}{$payslip->persian_month}{$payslip->employee->national_code}.pdf"),200,[
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="payslip-'.$payslip->persian_year.$payslip->persian_month.$payslip->employee->national_code.'".pdf"'
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage().$error->getFile().$error->getLine()]);
        }
    }

    public function excel_upload(Request $request): array
    {
        try {
            $import_errors = [];
            $response = [];
            $flag = 'success';
            $template = Contract::query()->with("payslip_template")->findOrFail($request->input("contract_id"))->payslip_template;
            if ($template == null)
                throw new Exception("قرارداد مورد نظر دارای قالب فیش حقوقی نمی باشد");
            $import = new ImportContractPaySlip($request->input("contract_id"));
            $import->import($request->file("excel_file"));
            if (count($import->getFails()) > 0){
                foreach ($import->getFails() as $fail)
                    $import_errors [] = $fail;
            }
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"national_code" => $failure->values()[0]];
                }
            }
            if (count($import->getResult()) === 0) {
                $flag = "fail";
                $message = "عملیات ناموفق! لطفا به قسمت خطای بارگذاری مرجعه نمایید";
            }
            elseif (count($import_errors) > 0) {
                $flag = "warning";
                $message = "عملیات به طور کامل انجام نشد! لطفا به قسمت خطای بارگذاری مرجعه نمایید";
            }
            else
                $message = "عملیات با موفقیت انجام شد";
            $response["result"] = $flag;
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"]["results"] = $import->getResult();
            $response["data"]["template"] = $template->toArray();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage()." ({$error->getLine()})";
            return $response;
        }
    }
}
