<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Models\SmsPhraseCategory;
use App\Models\UnregisteredEmployee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class UnregisteredEmployeeController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"UnregisteredEmployees");
        try {
            return view("staff.unregistered_employees",[
                "organizations" => $this->allowed_contracts("tree"),
                "employees" => UnregisteredEmployee::query()->whereIn("organization_id",$this->allowed_contracts()->pluck("id")->flatten()->unique())->get(),
                "sms_phrase_categories" => SmsPhraseCategory::query()->with("phrases")->get()
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function confirm($id,Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('confirm',"UnregisteredEmployees");
        try {
            DB::beginTransaction();
            $request->validate([
                "contract_id" => "required",
                "send_sms_permission" => "sometimes|nullable",
                "sms_text" => "required_if:send_sms_permission,true"
            ], [
                "contract_id.required" => "انتخاب سازمان و قرارداد الزامی می باشد",
                "sms_text" => "در صورت انتخاب ارسال پیامک، متن پیامک را باید جهت ارسال وارد نمایید"
            ]);
            $employee = UnregisteredEmployee::query()->findOrFail($id);
            $registered = Employee::employee($employee->national_code);
            $registering = ContractPreEmployee::employee($employee->national_code);
            if ($registered != null)
                throw new Exception("کد ملی ".$employee->national_code." قبلا در سامانه ثبت نام کرده است");
            elseif ($registering != null)
                throw new Exception("کد ملی ".$employee->national_code." در سامانه ثبت نام وجود دارد");
            else {
                ContractPreEmployee::query()->create([
                    "contract_id" => $request->input("contract_id"),
                    "user_id" => Auth::id(),
                    "name" => $employee->name,
                    "national_code" => $employee->national_code,
                    "mobile" => $employee->mobile,
                ]);
                if ($request->has("send_sms_permission"))
                    $this->send_sms([$employee->mobile], $request->input("sms_text"));
                $employee->delete();
                DB::commit();
                return redirect()->back()->with(["result" => "success", "message" => "saved"]);
            }
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function refuse($id,Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('reject',"UnregisteredEmployees");
        try {
            DB::beginTransaction();
            $request->validate([
                "send_sms_permission" => "sometimes|nullable",
                "sms_text" => "required_if:send_sms_permission,true"
            ], ["sms_text" => "در صورت انتخاب ارسال پیامک، متن پیامک را باید جهت ارسال وارد نمایید"]);
            $employee = UnregisteredEmployee::query()->findOrFail($id);
            if ($request->has("send_sms_permission"))
                $this->send_sms([$employee->mobile], $request->input("sms_text"));
            $employee->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success", "message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
