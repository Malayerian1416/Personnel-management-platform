<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Models\CompanyInformation;
use App\Models\Employee;
use App\Models\EmployeePaySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Mpdf\QrCode\QrCode;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;
use Mpdf\QrCode\Output;

class EmployeeRequestAutomationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"EmployeeRequestsAutomation");
        try {
            //dd(Automation::GetPermitted()->unique()->values());
            return view("staff.automation",[
                "records" => Automation::GetPermitted()->values(),
                "organizations" => $this->allowed_contracts("tree"),
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function confirm(Request $request): array
    {
        Gate::authorize('confirm',"EmployeeRequestsAutomation");
        try {
            DB::beginTransaction();
            $request->validate(["id" => "required",],["id.required" => "شماره درخواست مشخص نمی باشد",]);
            $automation = Automation::query()->with(["automationable"])->findOrFail($request->input("id"));
            if ($request->input("recipient") != "")
                $automation->automationable->update(["recipient" => $request->input("recipient")]);
            if ($request->input("borrower") != "")
                $automation->automationable->update(["borrower" => $request->input("borrower")]);
            if ($request->input("loan_amount") != "")
                $automation->automationable->update(["loan_amount" => $request->input("loan_amount")]);
            $automate_result = $automation->automate("forward",$request->input("comment"));
            switch ($automate_result["result"]){
                case "finished": {
                    $message = "درخواست مورد نظر با موفقیت تایید نهایی شد و در داشبورد پرسنل قرار گرفت";
                    $flag = "success";
                    break;
                }
                case "no_main_role": {
                    $message = "در گردش اتوماسیون تعیین کننده نهایی انتخاب نشده است";
                    $flag = "warning";
                    break;
                }
                case "mismatch": {
                    $message = "سمت شغلی شما در گردش اتوماسیون تعریف نشده است";
                    $flag = "warning";
                    break;
                }
                case "sent": {
                    $message = "درخواست مورد نظر با موفقیت تایید و ارسال شد";
                    $flag = "success";
                    $this->SendNotification($automate_result["message"]["users"],$automate_result["message"]["data"]);
                    break;
                }
                default:{
                    $message = "خطای سیستم! با پشتیبان تماس حاصل نمایید";
                    $flag = "warning";
                }
            }
            DB::commit();
            $response["result"] = $flag;
            $response["message"] = $message;
            $response["automations"] = Automation::GetPermitted()->values();
            $response["as"] = $automate_result;
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function reject(Request $request): array
    {
        Gate::authorize('reject',"EmployeeRequestsAutomation");
        try {
            DB::beginTransaction();
            $request->validate(["id" => "required"],["id.required" => "شماره درخواست مشخص نمی باشد"]);
            $automation = Automation::query()->with(["automationable"])->findOrFail($request->input("id"));
            $automate_result = $automation->automate("backward",$request->input("comment"));
            switch ($automate_result["result"]){
                case "finished": {
                    $message = "درخواست مورد نظر با موفقیت تایید نهایی شد و در داشبورد پرسنل قرار گرفت";
                    $flag = "success";
                    break;
                }
                case "no_main_role": {
                    $message = "در گردش اتوماسیون تعیین کننده نهایی انتخاب نشده است";
                    $flag = "warning";
                    break;
                }
                case "mismatch": {
                    $message = "سمت شغلی شما در گردش اتوماسیون تعریف نشده است";
                    $flag = "warning";
                    break;
                }
                case "referred": {
                    $message = "درخواست مورد نظر با موفقیت تایید و ارسال شد";
                    $flag = "success";
                    $this->SendNotification($automate_result["message"]["users"],$automate_result["message"]["data"]);
                    break;
                }
                default:{
                    $message = "خطای سیستم! با پشتیبان تماس حاصل نمایید";
                    $flag = "warning";
                }
            }
            DB::commit();
            $response["result"] = $flag;
            $response["message"] = $message;
            $response["automations"] = Automation::GetPermitted()->values();
            $response["as"] = $automate_result;
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function seen(Request $request): array|\Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate(["id" => "required"],["id.required" => "شماره درخواست مشخص نمی باشد"]);
            $automation = Automation::query()->with(["user","automationable","employee.automations.user","employee.automations.signs","employee.automations.automationable","signs.user.role","comments.user.role"])->findOrFail($request->input("id"));
            $automation->update(["is_read" => 1,"editable" => 0]);
            DB::commit();
            $response["automations"] = Automation::GetPermitted()->values();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function refresh_data(Request $request): array|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('details',"EmployeeRequestAutomationController");
        try {
            DB::beginTransaction();
            $request->validate(["id" => "required"],["id.required" => "شماره درخواست مشخص نمی باشد"]);
            $automation = Automation::query()->with(["user","automationable","employee.automations.user","employee.automations.signs","employee.automations.automationable","signs.user.role","comments.user.role"])->findOrFail($request->input("id"));
            $employee = Employee::query()->with(["contract.organization"])->findOrFail($automation->employee_id);
            $data = json_encode([
                "active_contract" => [
                    "organization_id" => $employee->contract->organization->id,
                    "organization_name" => $employee->contract->organization->name,
                    "contract_id" => $employee->contract->id,
                    "contract_name" => $employee->contract->name
                ],
                "active_salary_details" => $employee->active_salary_details(),
                "payslip" => EmployeePaySlip::Last($employee->id),
                "active_contract_date" => $employee->active_contract_date()
            ],JSON_UNESCAPED_UNICODE);
            $automation->automationable->update(["data" => $data]);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "اطلاعات با موفقیت بروزرسانی شد";
            $response["automations"] = Automation::GetPermitted()->values();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function preview($id){
        $automation = Automation::query()->with(["user","automationable","employee.automations.user","employee.automations.signs","employee.automations.automationable","signs.user.role","comments.user.role"])->findOrFail($id);
        $automation->update(["is_read" => 1]);
        $qrCode = new QrCode(route("Validation.direct",["i_number" => $automation->automationable->i_number]));
        $output = new Output\Png();
        $pdf = PDF::loadView("layouts.pdf.{$automation->application_class}", [
            "application" => $automation,
            "background" => base64_encode(file_get_contents(public_path("images/A4.jpg"))),
            "qrCode" => base64_encode($output->output($qrCode, 100, [255, 255, 255], [0, 0, 0])),
            "company_information" => CompanyInformation::query()->first(),
            "logo" => base64_encode(file_get_contents(public_path("/images/logo.png"))),
            "number" => preg_replace("/[^0-9]/", "", $automation->automationable->i_number ),
            "sign" => false
        ], [], [
            'format' => "A4-P"
        ]);
        return response()->make($pdf->stream("request.pdf"),200,[
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="request.pdf"'
        ]);
    }
    public function get_latest(): \Illuminate\Database\Eloquent\Collection|array
    {
        try {
            return Automation::GetPermitted();
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function edit_employee_information(Request $request): array
    {
//        Gate::authorize('edit_employee_information',"EmployeeRequestsAutomation");
        try {
            DB::beginTransaction();
            $request->validate([
                "automation_id" => "required",
                "id" => "required",
                "first_name" => "required",
                "last_name" => "required",
                "id_number" => "required",
                "father_name" => "required",
                "gender" => "required",
                "mobile" => "required",
                "job_seating" => "required",
                "job_title" => "required",
                "start" => "required|jdate:Y/m/d",
                "end" => "required|jdate:Y/m/d|jdate_after:{$request->input('start')},Y/m/d",
            ],[
                "automation_id.required" => "شماره درخواست مشخص نمی باشد",
                "id.required" => "شماره پرسنل مشخص نمی باشد",
                "first_name.required" => "",
                "last_name.required" => "",
                "id_number.required" => "",
                "father_name.required" => "",
                "gender.required" => "",
                "mobile.required" => "",
                "job_seating.required" => "",
                "job_title.required" => "",
                "start.required" => "",
                "end.required" => "",
                "start.jdate" => "تاریخ شروع معتبر نمی باشد",
                "end.jdate" => "تاریخ پایان معتبر نمی باشد",
                "end.jdate_after" => "تاریخ شروع باید قبل از تاریخ پایان باشد",
            ]);
            $employee = Employee::query()->with(["contract_extensions","contract.organization"])->findOrFail($request->input("id"));
            $employee->update([
                "first_name" => $request->input("first_name"),
                "last_name" => $request->input("last_name"),
                "id_number" => $request->input("id_number"),
                "father_name" => $request->input("father_name"),
                "gender" => $request->input("gender"),
                "mobile" => $request->input("mobile"),
                "job_seating" => $request->input("job_seating"),
                "job_title" => $request->input("job_title"),
            ]);
            $contract_date = $employee->contract_extensions->where("start","=",$this->Gregorian($request->input("start")))->where("end","=",$this->Gregorian($request->input("end")))->first();
            if ($contract_date == null) {
                if ($employee->contract_extensions())
                    $employee->contract_extensions()->update(["active" => 0]);
                $employee->contract_extensions()->create([
                    "user_id" => Auth::id(),
                    "start" => $this->Gregorian($request->input("start")),
                    "end" => $this->Gregorian($request->input("end")),
                    "active" => 1
                ]);
            }
            $automation = Automation::query()->with(["automationable"])->findOrFail($request->input("automation_id"));
            $data = json_encode([
                "active_contract" => [
                    "organization_id" => $employee->contract->organization->id,
                    "organization_name" => $employee->contract->organization->name,
                    "contract_id" => $employee->contract->id,
                    "contract_name" => $employee->contract->name
                ],
                "active_salary_details" => $employee->active_salary_details(),
                "payslip" => EmployeePaySlip::Last($employee->id),
                "active_contract_date" => $employee->active_contract_date()
            ],JSON_UNESCAPED_UNICODE);
            $automation->automationable->update(["data" => $data]);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = $automation;
            $response["automations"] = Automation::GetPermitted();
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["automations"] = Automation::GetPermitted();
            return $response;
        }
    }
}
