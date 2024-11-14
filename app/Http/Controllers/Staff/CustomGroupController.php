<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ExportCustomGroupExcel;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomGroupRequest;
use App\Imports\ImportCustomGroupExcel;
use App\Models\CustomGroup;
use App\Models\CustomGroupEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CustomGroupController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"CustomGroups");
        try {
            $groups = CustomGroup::query()->with(["employees","user"])->whereHas("user",function ($query){
                $query->where("id","=",Auth::id());
            })->get();
            return view("staff.custom_groups",["groups" => $groups]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(CustomGroupRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"CustomGroups");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $custom_group = CustomGroup::query()->create($validated);
            foreach (json_decode($validated["employees"],true) as $employee)
                $custom_group->employees()->create([
                    "employee_id" => $employee["id"]
                ]);
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
        Gate::authorize('edit',"CustomGroups");
        try {
            $group = CustomGroup::query()->with(["employees.employee"])->findOrFail($id);
            return view("staff.edit_custom_group",["group" => $group]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(CustomGroupRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"CustomGroups");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $custom_group = CustomGroup::query()->findOrFail($id);
            $custom_group->update($validated);
            if (strlen($validated["employees"]) > 10) {
                foreach (json_decode($validated["employees"], true) as $employee)
                    $custom_group->employees()->updateOrCreate(["group_id" => $id, "employee_id" => $employee["id"]], [
                        "employee_id" => $employee["id"]
                    ]);
            }
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
        Gate::authorize('delete',"CustomGroups");
        try {
            DB::beginTransaction();
            $custom_group = CustomGroup::query()->findOrFail($id);
            $custom_group->employees()->delete();
            $custom_group->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function delete_employee(Request $request): array
    {
        try {
            DB::beginTransaction();
            $response = [];
            if ($request->has("member_id")) {
                $request->validate(
                    ["member_id" => "required|numeric"],
                    ["member_id.required" => "انتخاب پرسنل از لیست الزامی می باشد","member_id.numeric" => "فرمت مقدار ارسال شده صحیح نمی باشد"]
                );
                $employee = CustomGroupEmployee::query()->findOrFail($request->input("member_id"));
                $group_id = $employee->group_id;
                $employee->delete();
                $group = CustomGroup::query()->with(["employees.employee"])->findOrFail($group_id);
                $response["result"] = "success";
                $response["message"] = "عضویت پرسنل مورد نظر با موفقیت حذف گردید";
                $response["data"] = $group->employees;
            }
            elseif ($request->has("group_id")){
                $request->validate(
                    ["group_id" => "required|numeric"],
                    ["group_id.required" => "انتخاب پرسنل از لیست الزامی می باشد","group_id.numeric" => "فرمت مقدار ارسال شده صحیح نمی باشد"]
                );
                $group = CustomGroup::query()->with(["employees.employee"])->findOrFail($request->input("group_id"));
                $group->employees()->delete();
                $response["result"] = "success";
                $response["message"] = "عضویت تمامی پرسنل گروه با موفقیت حذف گردید";
                $response["data"] = $group->employees();
            }
            DB::commit();
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

    public function excel_download(): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            return Excel::download(new ExportCustomGroupExcel, 'custom_group.xlsx');
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function excel_upload(Request $request): array
    {
        try {
            $request->validate(
                ["upload_file" => "required|mimes:xlsx,xls"],
                ["upload_file.required" => "فایلی باگذاری نشده است","upload_file.mimes" => "فرمت فایل باگذاری شده صحیح نمی باشد"]
            );
            $response = [];
            $import_errors = [];
            $import = new ImportCustomGroupExcel;
            $import->import($request->file("upload_file"));
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
            if (count($import_errors) > 0)
                $message = "عملیات بارگذاری فایل وضعیت با موفقیت انجام شد اما ثبت اطلاعات فایل به طور کامل انجام نشد";
            else
                $message = "عملیات بارگذاری فایل وضعیت با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage().$error->getLine();
            $response["data"] = [];
            return $response;
        }
    }
    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $group = CustomGroup::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($group)]);
    }
}
