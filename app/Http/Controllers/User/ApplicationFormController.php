<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationFormRequest;
use App\Models\ApplicationForm;
use App\Models\Automation;
use App\Models\BackgroundCheckApplication;
use App\Models\CompanyInformation;
use App\Models\Employee;
use App\Models\EmployeePaySlip;
use App\Models\EmploymentCertificateApplication;
use App\Models\LoanPaymentConfirmationApplication;
use App\Models\OccupationalMedicineApplication;
use App\Models\PersonnelAppointmentForm;
use App\Models\SettlementFormApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mpdf\QrCode\QrCodeException;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

class ApplicationFormController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $requests = Automation::query()->with(["user","current_role","employee","automationable"])->whereHas("employee",function ($query){
                $query->where("id","=",Auth::user()->employee->id);
            })->orderBy("updated_at","desc")->get();
            return view("user.requests",["requests" => $requests]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view("user.new_request",["applications" => ApplicationForm::all(["id","name","application_form_type"])]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function store(ApplicationFormRequest $request): \Illuminate\Http\RedirectResponse
    {
        try{
            $validated = $request->validated();
            $employee = Employee::query()->with(["contract.organization"])->findOrFail(Auth::user()->employee->id);
            $automation = ApplicationForm::MakeAutomation($validated["application_form_type"]);
            $data = json_encode([
                "active_contract" => [
                    "organization_id" => $employee->contract->organization->id,
                    "organization_name" => $employee->contract->organization->name,
                    "contract_id" => $employee->contract->id,
                    "contract_name" => $employee->contract->name
                    ],
                "payslip" => EmployeePaySlip::Last(Auth::user()->employee->id),
                "active_contract_date" => $employee->active_contract_date(),
                "active_salary_details" => $employee->active_salary_details()
            ],JSON_UNESCAPED_UNICODE);
            $class = '';
            $pre_number = implode('',Str::matchAll("/[A-Z]+/",$validated["application_form_type"])->toArray());
            switch ($validated["application_form_type"]){
                case "PersonnelAppointmentForm":{
                    $class = PersonnelAppointmentForm::class;
                    break;
                }
                case "SettlementFormApplication":{
                    $class = SettlementFormApplication::class;
                    break;
                }
                case "LoanPaymentConfirmationApplication":{
                    $class = LoanPaymentConfirmationApplication::class;
                    break;
                }
                case "EmploymentCertificateApplication":{
                    $class = EmploymentCertificateApplication::class;
                    break;
                }
                case "OccupationalMedicineApplication":{
                    $class = OccupationalMedicineApplication::class;
                    break;
                }
                case "BackgroundCheckApplication":{
                    $class = BackgroundCheckApplication::class;
                    break;
                }
            }
            do
                $number = $pre_number.verta()->format("Ynj").rand(14238765,99999999);
            while ($class::query()->where("i_number","=",$number)->get()->isNotEmpty());
            $validated["user_id"] = Auth::id();
            $validated["employee_id"] = Auth::user()->employee->id;
            $validated["data"] = $data;
            $validated["i_number"] = $number;
            $validated["loan_amount"] = Str::replace(",","",$validated["loan_amount"]);
            $form = $class::query()->create($validated);
            $form->automation()->create([
                "employee_id" => Auth::user()->employee->id,
                "contract_id" => Auth::user()->employee->contract_id,
                "current_role_id" => $automation["current_role_id"],
                "current_priority" => $automation["current_priority"],
                "user_id" => Auth::id(),
                "flow" => $automation["details"],
            ]);
            DB::commit();
            $notification["message"]["users"] = User::RecipientAutomationCreation(Auth::user()->employee->contract_id);
            $notification["message"]["data"]["message"] = "رکورد جدید در اتوماسیون نامه های اداری دریافت شد";
            $notification["message"]["data"]["type"] = "request";
            $notification["message"]["data"]["action"] = route("EmployeeRequestsAutomation.index");
            $this->SendNotification($notification["message"]["users"],$notification["message"]["data"]);
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function edit($id){

    }
    public function update(){

    }
    public function destroy(){

    }
    public function download_pdf($id){
        try {
            $application = Automation::with(["employee", "automationable"])->findOrFail($id);
            $qrCode = new QrCode(route("Validation.direct",["i_number" => $application->automationable->i_number]));
            $output = new Output\Png();
            $sign = $application->GetMainUser();
            $pdf = PDF::loadView("layouts.pdf.{$application->application_class}", [
                "application" => $application,
                "background" => base64_encode(file_get_contents(public_path("images/A4.jpg"))),
                "logo" => base64_encode(file_get_contents(public_path("/images/logo.png"))),
                "number" => preg_replace("/[^0-9]/", "", $application->automationable->i_number ),
                "qrCode" => base64_encode($output->output($qrCode, 100, [255, 255, 255], [0, 0, 0])),
                "company_information" => CompanyInformation::query()->first(),
                "sign" => $sign ? ["role" => $sign->user->role->name,"name" => $sign->user->name,"sign" => $sign->user->GetSign()] : []
            ], [], [
                'format' => "A4-P"
            ]);
            $application->update(["is_read" => 1]);
            $pdf->download("{$application->automationable->i_number}.pdf");
        }
        catch (QrCodeException $ex) {
            return redirect()->back()->withErrors("barcode","خطای سیستم!");
        }
    }
}
