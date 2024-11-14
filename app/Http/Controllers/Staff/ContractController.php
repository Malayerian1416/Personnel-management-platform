<?php

namespace App\Http\Controllers\Staff;

use App\Exports\PreContractEmployeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Imports\PreContractEmployeeImport;
use App\Models\Contract;
use App\Models\ContractPreEmployee;
use App\Models\Organization;
use App\Rules\NationalCodeChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ContractController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Contracts");
        try {
            return view("staff.contracts", [
                "organizations" => Organization::all(),
                "contracts" => Contract::query()->where("parent_id","=",null)
                    ->with(["user", "organization", "children","parent","employees","pre_employees"])->orderBy("id","asc")->get()
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function store(ContractRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Contracts");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["start_date"] = $this->Gregorian($validated["start_date"]);$validated["end_date"] = $this->Gregorian($validated["end_date"]);
            DB::beginTransaction();
            $contract = Contract::query()->create($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_docs')->put($contract->id, $file);
                $contract->update(["files" => 1]);
            }
            if ($request->has("is_parent")){
                $children = json_decode($validated["children_subset_list"],true);
                foreach ($children as $child){
                    $employees = $child["employees"];
                    $subcontract = Contract::query()->create([
                        "user_id" => Auth::id(),
                        "parent_id" => $contract->id,
                        "organization_id" => $validated["organization_id"],
                        "name" => $child["name"],
                    ]);
                    if (count($employees)){
                        foreach ($employees as $employee)
                            $subcontract->pre_employees()->updateOrCreate(["national_code" => $employee["national_code"]],[
                                "user_id" => Auth::id(),
                                "name" => $employee["name"],
                                "national_code" => $employee["national_code"],
                                "mobile" => $employee["mobile"]
                            ]);
                    }
                }
            }
            else{
                if ($request->has("employees")){
                    $employees = json_decode($validated["employees"],true);
                    if (count($employees)){
                        foreach ($employees as $employee)
                            $contract->pre_employees()->updateOrCreate(["national_code" => $employee["national_code"]],[
                                "user_id" => Auth::id(),
                                "name" => $employee["name"],
                                "national_code" => $employee["national_code"],
                                "mobile" => $employee["mobile"]
                            ]);
                    }
                }
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
        Gate::authorize('edit',"Contracts");
        try {
            $contract = Contract::query()->with(["organization","children.pre_employees.user","pre_employees.user","parent"])->findOrFail($id);
            if ($contract->parent_id)
                return redirect()->back()->withErrors(["logical" => "جهت ویرایش زیرمجموعه از طریق قرارداد والد آن اقدام نمایید"]);
            else {
                return view("staff.edit_contract", [
                    "organizations" => Organization::all(),
                    "contracts" => Contract::all(),
                    "contract" => $contract
                ]);
            }
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function update(ContractRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Contracts");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["start_date"] = $this->Gregorian($validated["start_date"]);$validated["end_date"] = $this->Gregorian($validated["end_date"]);
            DB::beginTransaction();
            $contract = Contract::query()->findOrFail($id);
            $contract->update($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_docs')->put($contract->id, $file);
                $contract->update(["files" => 1]);
            }
            if ($contract->is_parent){
                $children = json_decode($validated["children_subset_list"],true);
                foreach ($children as $child){
                    $employees = $child["employees"];
                    $subcontract = Contract::query()->create([
                        "user_id" => Auth::id(),
                        "parent_id" => $contract->id,
                        "organization_id" => $validated["organization_id"],
                        "name" => $child["name"],
                    ]);
                    if (count($employees)){
                        foreach ($employees as $employee)
                            $subcontract->pre_employees()->updateOrCreate(["national_code" => $employee["national_code"]],[
                                "user_id" => Auth::id(),
                                "name" => $employee["name"],
                                "national_code" => $employee["national_code"],
                                "mobile" => $employee["mobile"]
                            ]);
                    }
                }
            }
            else{
                if ($request->has("employees")){
                    $employees = json_decode($validated["employees"],true);
                    if (count($employees)){
                        foreach ($employees as $employee)
                            $contract->pre_employees()->updateOrCreate(["national_code" => $employee["national_code"]],[
                                "user_id" => Auth::id(),
                                "name" => $employee["name"],
                                "national_code" => $employee["national_code"],
                                "mobile" => $employee["mobile"]
                            ]);
                    }
                }
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
        Gate::authorize('delete',"Contracts");
        try {
            DB::beginTransaction();
            $contract = Contract::query()->findOrFail($id);
            if ($contract->pre_employees()->exists() || $contract->employees()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            Storage::disk("contract_docs")->deleteDirectory($id);
            $contract->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function subset_destroy(Request $request): array
    {
        try {
            $request->validate(["operation_id" => "required"], ["operation_id.required" => "قراردادی وجود ندارد"]);
            DB::beginTransaction();
            $response = [];
            $contract = Contract::query()->findOrFail($request->operation_id);
            if ($contract->pre_employees()->where("registered","=",1)->exists() || $contract->employees()->exists()){
                $result = "fail";
                $message = "امکان حذف این زیرمجموعه به دلیل موجود بودن پرسنل ثبت نام شده و یا در حال ثبت نام وجود ندارد";
                $data = [];
            }
            else{
                Storage::disk("contract_docs")->deleteDirectory($request->operation_id);
                $result = "success";
                $message = "عملیات  حذف زیرمجموعه با موفقیت انجام شد";
                $data = Contract::query()->with(["organization","children.pre_employees.user","pre_employees.user","parent"])->findOrFail($contract->parent_id)->children;
                $contract->delete();
            }
            DB::commit();
            $response["result"] = $result;
            $response["message"] = $message;
            $response["data"] = $data;
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
    public function excel_download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new PreContractEmployeesExport, 'pre_contract_employees.xlsx');
    }
    public function excel_upload(Request $request): array
    {
        try {
            $request->validate(
                ["excel_file" => "required|mimes:xlsx,xls"],
                ["excel_file.required" => "فایلی بارگذاری نشده است","excel_file.mimes" => "فرمت فایل بارگذاری شده صحیح نمی باشد"]
            );
            $response = [];
            $import_errors = [];
            $import = new PreContractEmployeeImport;
            $import->import($request->file("excel_file"));
            if (count($import->getFails()) > 0){
                foreach ($import->getFails() as $fail)
                    $import_errors [] = $fail;
            }
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"national_code" => $failure->values()[1]];
                }
            }
            if (count($import_errors) > 0 && count($import->getResult()) > 0) {
                $message = "عملیات بارگذاری لیست پرسنل به طور کامل انجام نشد. لطفا به قسمت مشاهده خطای بارگذاری مراجعه نمایید";
                $result = "fail";
            }
            elseif (count($import_errors) > 0 && count($import->getResult()) == 0 || count($import->getResult()) == 0) {
                $message = "عملیات بارگذاری لیست پرسنل انجام نشد. لطفا به قسمت مشاهده خطای بارگذاری مراجعه نمایید";
                $result = "fail";
            }
            else {
                $message = "عملیات بارگذاری لیست پرسنل با موفقیت انجام شد";
                $result = "success";
            }
            $response["result"] = $result;
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }
    public function status($id): \Illuminate\Http\RedirectResponse
    {
        return redirect()->back()->with(["result" => "success","message" => $this->activation(Contract::query()->findOrFail($id))]);
    }
    public function download_docs($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $status = $this->download($id,"contract_docs","private");
        if ($status["success"]) {
            $zip_file = Storage::disk("contract_docs")->path("/zip/{$status["folder"]}/{$status["name"]}");
            $zip_file_name = "contract_docs_" . verta()->format("Y-m-d H-i-s") . ".zip";
            return Response::download($zip_file,$zip_file_name,[],'inline');
        }
        else
            return redirect()->back()->withErrors(["logical" => $status["message"]]);
    }
    public function pre_employee_operations(Request $request,$type): array
    {
        try {
            $response = [];
            $result = '';
            $message = '';
            $data = [];
            DB::beginTransaction();
            switch ($type){
                case "add":{
                    $request->validate([
                        "operation_id" => "required",
                        "new_name" => "required",
                        "new_national_code" => ["required","unique:contract_pre_employees,national_code",new NationalCodeChecker()]
                    ], [
                        "operation_id.required" => "پرسنلی انتخاب نشده است",
                        "new_name.required" => "درج نام پرسنل الزامی می باشد",
                        "new_national_code.required" => "درج کد ملی الزامی می باشد",
                        "new_national_code.unique" => "کد ملی وارد شده تکراری می باشد"
                    ]);
                    $id = $request->input("operation_id");
                    ContractPreEmployee::query()->create([
                        "contract_id" => $id,
                        "user_id" => Auth::id(),
                        "name" => $request->input("new_name"),
                        "national_code" => $request->input("new_national_code"),
                        "mobile" => $request->input("new_mobile"),
                    ]);
                    $data = ContractPreEmployee::query()->with("user")->where("contract_id","=",$id)->get()->toArray();
                    $result = "success";
                    $message = "عملیات ایجاد پرسنل با موفقیت انجام شد";
                    break;
                }
                case "edit":{
                    $request->validate([
                        "operation_id" => "required",
                        "name" => "required",
                        "national_code" => ["required",new NationalCodeChecker()]
                    ], [
                        "operation_id.required" => "پرسنلی انتخاب نشده است",
                        "name.required" => "درج نام پرسنل الزامی می باشد",
                        "national_code.required" => "درج کد ملی الزامی می باشد",
                    ]);
                    $id = $request->input("operation_id");
                    $employee = ContractPreEmployee::query()->findOrFail($id);
                    $employee->update([
                        "user_id" => Auth::id(),
                        "name" => $request->input("name"),
                        "national_code" => $request->input("national_code"),
                        "mobile" => $request->input("mobile"),
                    ]);
                    $data = ContractPreEmployee::query()->with("user")->where("contract_id","=",$employee->contract_id)->get()->toArray();
                    $result = "success";
                    $message = "عملیات ویرایش پرسنل با موفقیت انجام شد";
                    break;
                }
                case "delete":{
                    $request->validate(["operation_id" => "required"], ["operation_id.required" => "پرسنلی انتخاب نشده است"]);
                    $id = $request->input("operation_id");
                    $employee = ContractPreEmployee::query()->findOrFail($id);
                    if ($employee->registered){
                        $result = "fail";
                        $message = "پرسنل انتخاب شده در وضعیت ثبت نام شده قرار دارد";
                    }
                    else {
                        $employee->delete();
                        $result = "success";
                        $message = "عملیات حذف پرسنل با موفقیت انجام شد";
                    }
                    $data = ContractPreEmployee::query()->with("user")->where("contract_id", "=", $employee->contract_id)->get()->toArray();
                    break;
                }
                case "delete_all":{
                    $request->validate(["operation_id" => "required","delete_all_employee" => "required"],
                        ["operation_id.required" => "قراردادی انتخاب نشده است","delete_all_employee.required" => "نوع حذف اطلاعات مشخص نمی باشد"]);
                    $id = $request->input("operation_id");
                    $contract = Contract::query()->with("pre_employees")->findOrFail($id);
                    if ($request->input("delete_all_employee") === "all")
                        $contract->pre_employees()->delete();
                    else
                        $contract->pre_employees()->where("registered","=",0)->delete();
                    $result = "success";
                    $message = "عملیات حذف پرسنل با موفقیت انجام شد";
                    $data = ContractPreEmployee::query()->with("user")->where("contract_id", "=", $contract->id)->get()->toArray();
                    break;
                }
            }
            DB::commit();
            $response["result"] = $result;
            $response["message"] = $message;
            $response["data"] = $data;
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
}
