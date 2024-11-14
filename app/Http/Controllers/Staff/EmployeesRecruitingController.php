<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeesRecruitingConfirmRequest;
use App\Http\Requests\EmployeesRecruitingRefuseRequest;
use App\Http\Requests\EmployeesRecruitingReloadRequest;
use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Models\SmsPhraseCategory;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EmployeesRecruitingController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"EmployeesRecruiting");
        try {
            $employees = ContractPreEmployee::NewRegistration($this->allowed_contracts()->pluck("contracts.*.id")->flatten()->unique());
            $sms_phrase_categories = SmsPhraseCategory::query()->with("phrases")->get();
            return view("staff.employees_recruiting",[
                "employees" => $employees,
                "sms_phrase_categories" => $sms_phrase_categories,
                "organizations" => $this->allowed_contracts("tree")
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function confirm(EmployeesRecruitingConfirmRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('confirm',"EmployeesRecruiting");
        try {
            DB::beginTransaction();
            $mobiles = [];
            $validated = $request->validated();
            foreach ($validated["employees"] as $employee){
                $pre_employee = ContractPreEmployee::query()->findOrFail($employee);
                $db_employee = Employee::employee($pre_employee->national_code);
                if ($db_employee) {
                    $db_employee->update([
                        "user_id", Auth::id(),
                        "initial_start" => $this->Gregorian($validated["start_date"]),
                        "initial_end" => $this->Gregorian($validated["end_date"]),
                        "documents" => 1
                    ]);
                    User::query()->updateOrCreate(["employee_id" => $db_employee->id],[
                        "user_id" => Auth::id(),
                        "name" => $db_employee->first_name . " " . $db_employee->last_name,
                        "gender" => $db_employee->gender,
                        "username" => $db_employee->national_code,
                        "password" => Hash::make($db_employee->national_code),
                        "email" => Hash::make($db_employee->national_code),
                        "mobile" => $db_employee->mobile,
                        "is_user" => 1,
                        "is_staff" => 0,
                        "is_admin" => 0,
                        "is_super_user" => 0
                    ]);
                    isset($validated["send_sms_permission"]) ? $mobiles [] = $db_employee->mobile : null;
                    $pre_employee->update(["approved" => 1]);
                }
            }
            if (count($mobiles) > 0) $this->send_sms($mobiles, $validated["sms_text"]);
            DB::commit();
            return redirect()->back()->with(["result" => "registered"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }


    public function refuse(EmployeesRecruitingRefuseRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('reject',"EmployeesRecruiting");
        try {
            DB::beginTransaction();
            $mobiles = [];
            $validated = $request->validated();
            foreach ($validated["employees"] as $employee) {
                $pre_employee = ContractPreEmployee::query()->findOrFail($employee);
                $db_employee = Employee::employee($pre_employee->national_code);
                if ($db_employee && $pre_employee) {
                    Storage::disk("employee_docs")->deleteDirectory("/{$db_employee->national_code}");
                    $db_employee->delete();
                    if (isset($validated["delete_employees"]))
                        $pre_employee->delete();
                    else
                        $pre_employee->update([
                            "verify" => null,
                            "verify_timestamp" => null,
                            "tracking_code" => null,
                            "registered" => 0,
                            "registration_date" => null,
                            "to_reload" => 0,
                            "reloaded" => 0,
                            "reload_date" => null
                        ]);
                    isset($validated["send_sms_permission"]) ? $mobiles [] = $db_employee->mobile : null;
                }
            }
            if (count($mobiles) > 0) $this->send_sms($mobiles, $validated["sms_text"]);
            DB::commit();
            return redirect()->back()->with(["result" => "unregistered"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function reload_data(EmployeesRecruitingReloadRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('reload_data',"EmployeesRecruiting");
        try {
            DB::beginTransaction();
            $mobiles = [];
            $validated = $request->validated();
            foreach ($validated["employees"] as $employee) {
                $pre_employee = ContractPreEmployee::query()->findOrFail($employee);
                if ($pre_employee != null) {
                    $pre_employee->reload_data()->updateOrCreate(["reloadable_id" => $pre_employee->id, "national_code" => $pre_employee->national_code], [
                        "national_code" => $pre_employee->national_code,
                        "db_titles" => isset($validated["db_titles"]) ? json_encode($validated["db_titles"], JSON_UNESCAPED_UNICODE) : null,
                        "doc_titles" => isset($validated["doc_titles"]) ? json_encode($validated["doc_titles"], JSON_UNESCAPED_UNICODE) : null,
                    ]);
                    isset($validated["send_sms_permission"]) ? $mobiles [] = $pre_employee->mobile : null;
                }
                else
                    throw new Exception("برای پرسنل انتخاب شده اطلاعات ثبت نام وجود ندارد");
            }
            if (count($mobiles) > 0) $this->send_sms($mobiles, $validated["sms_text"]);
            DB::commit();
            return redirect()->back()->with(["result" => "employee_data_reload"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
