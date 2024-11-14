<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\EmployeeDataRequest;
use App\Models\SmsPhraseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class RefreshDataEmployeeController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $allowed_contracts = Contract::GetPermitted();
            if (User::UserType() == "admin")
                $refresh_employees = EmployeeDataRequest::query()->with(["employee.contract.organization","user"])->where("is_loaded","=",1)->get();
            else
                $refresh_employees = EmployeeDataRequest::query()->with(["user","employee.contract.organization"])->whereHas("employee", function($query) use($allowed_contracts){
                $query->whereIn("contract_id",$allowed_contracts);
            })->where("is_loaded","=",1)->get();
            return view("staff.refresh_data_employees",[
                "refresh_employees" => $refresh_employees,
                "sms_phrase_categories" => SmsPhraseCategory::query()->with("phrases")->get()
            ]);
        }
        catch (Throwable $error) {
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function confirm($id,Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "send_sms_permission" => "sometimes|nullable",
                "sms_text" => "required_if:send_sms_permission,true"
            ], ["sms_text" => "در صورت انتخاب ارسال پیامک، متن پیامک را باید جهت ارسال وارد نمایید"]);
            $refresh_data = EmployeeDataRequest::query()->with("employee")->findOrFail($id);
            if ($request->has("send_sms_permission"))
                $this->send_sms([$refresh_data->employee->mobile], $request->input("sms_text"));
            $refresh_data->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success", "message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function refuse($id,Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "title" => "required",
                "send_sms_permission" => "sometimes|nullable",
                "sms_text" => "required_if:send_sms_permission,true"
            ], [
                "title.required" => "درج متن نمایش در درخواست الزامی می باشد",
                "sms_text" => "در صورت انتخاب ارسال پیامک، متن پیامک را باید جهت ارسال وارد نمایید"
            ]);
            $refresh_data = EmployeeDataRequest::query()->with("employee")->findOrFail($id);
            if ($request->has("send_sms_permission"))
                $this->send_sms([$refresh_data->employee->mobile], $request->input("sms_text"));
            $refresh_data->update(["is_loaded" => 0,"reload_date" => null,"title" => $request->input("title")]);
            DB::commit();
            return redirect()->back()->with(["result" => "success", "message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
