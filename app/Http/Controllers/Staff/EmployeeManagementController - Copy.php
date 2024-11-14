<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ExportContractDateExcel;
use App\Exports\ExportCustomGroupExcel;
use App\Exports\ExportEmployeeCustomList;
use App\Exports\ExportNewEmployee;
use App\Http\Controllers\Controller;
use App\Imports\ImportContractDateExcel;
use App\Imports\ImportCustomGroupExcel;
use App\Imports\ImportNewEmployee;
use App\Models\ApplicationForm;
use App\Models\Automation;
use App\Models\BackgroundCheckApplication;
use App\Models\CompanyInformation;
use App\Models\Contract;
use App\Models\ContractConversion;
use App\Models\ContractExtension;
use App\Models\ContractPreEmployee;
use App\Models\CustomGroup;
use App\Models\DbKeyword;
use App\Models\Employee;
use App\Models\EmployeeDataRequest;
use App\Models\EmployeePaySlip;
use App\Models\EmploymentCertificateApplication;
use App\Models\LoanPaymentConfirmationApplication;
use App\Models\OccupationalMedicineApplication;
use App\Models\Organization;
use App\Models\PersonnelAppointmentForm;
use App\Models\SettlementFormApplication;
use App\Models\SmsPhraseCategory;
use App\Models\TicketRoom;
use App\Models\User;
use App\Rules\NationalCodeChecker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use \Illuminate\Support\Str;
use Throwable;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class EmployeeManagementController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"EmployeesManagement");
        try {
            return view("staff.employees_management", [
                "organizations" => $this->allowed_contracts("tree"),
                "custom_groups" => $this->allowed_groups()->toArray(),
                "sms_phrase_categories" => SmsPhraseCategory::query()->with("phrases")->get(),
                "applications" => ApplicationForm::all()
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    function get_employees(Request $request): array
    {
        try {
            $request->validate([
                "reference_type" => ["required",Rule::in(["contract","group"])],
                "reference_id" => "required"
            ],[
                "reference_type.required" => "نوع مرجع اطلاعات نامشخص می باشد",
                "reference_type.in" => "نوع مرجع اطلاعات نامشخص می باشد",
                "reference_id.required" => "شناسه مرجع اطلاعات نامشخص می باشد",
            ]);
            $type = $request->input("reference_type");
            $id = $request->input("reference_id");
            $response["result"] = "success";
            switch ($type){
                case "contract":{
                    $response["employees"] = Employee::query()->with(["contract.organization","registrant_user","user"])
                        ->where("contract_id","=",$id)->get()->toArray();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($id)->toArray();
                    $response["employees"] = Employee::query()->with(["contract.organization","registrant_user","user"])
                        ->whereIn("id",array_column($group["employees"],"employee_id"))->get()->toArray();
                    break;
                }
            }
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    function find_employees(Request $request): array
    {
        try {
            $keyword = $request->input("keyword");
            is_numeric($keyword) ? $keyword = $keyword."%" : $keyword = "%".$keyword."%";
            $response["result"] = "success";
            $exist_employees = Employee::find($keyword);
            $register_employees = ContractPreEmployee::find($keyword);
            $exist_employees->isNotEmpty() ? $response["employees"] = $exist_employees : $response["registration"] = $register_employees;
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function excel_download($option,$data = null)
    {
        try {
            switch ($option){
                case "NewEmployee": return Excel::download(new ExportNewEmployee, 'new_employees.xlsx');
                case "NationalCode": return Excel::download(new ExportCustomGroupExcel, 'employees_national_code.xlsx');
                case "ContractDate": return Excel::download(new ExportContractDateExcel($data), 'contract_dates.xlsx');
            }
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function excel_upload(Request $request, $option): array|\Illuminate\Http\RedirectResponse
    {
        try {
            $import_errors = [];
            $response = [];
            $flag = 'success';
            $message = '';
            $import = [];
            switch ($option){
                case "NationalCode":{
                    $import = new ImportCustomGroupExcel;
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
                    break;
                }
                case "ContractDate":{
                    $import = new ImportContractDateExcel();
                    $import->import($request->file("date_file"));
                    if (count($import->getFails()) > 0){
                        foreach ($import->getFails() as $fail)
                            $import_errors [] = $fail;
                    }
                    if (count($import->failures()->toArray()) > 0){
                        foreach ($import->failures() as $failure){
                            foreach ($failure->errors() as $error)
                                $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[0]];
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
                    break;
                }
                case "NewEmployee":{
                    $import = new ImportNewEmployee;
                    $import->import($request->file("excel_file"));
                    if (count($import->getFails()) > 0){
                        foreach ($import->getFails() as $fail)
                            $import_errors [] = $fail;
                    }
                    if (count($import->failures()->toArray()) > 0){
                        foreach ($import->failures() as $failure){
                            foreach ($failure->errors() as $error)
                                $import_errors [] = ["row" => $failure->row(),"message" => $error,"national_code" => $failure->values()[2]];
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
                    break;
                }
            }
            $response["result"] = $flag;
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["employees"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function add_employee(Request $request): array
    {
        Gate::authorize('add_new_item',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $message = "";
            $request->validate([
                "contract_id" => "required|numeric",
                "type" => ["required",Rule::in(["individual","group"])],
                "employees" => ["required_if:type,group","nullable"],
                "first_name" => ["required_if:type,individual","nullable"],
                "last_name" => ["required_if:type,individual","nullable"],
                "national_code" => ["required_if:type,individual","nullable", new NationalCodeChecker(),"unique:employees,national_code"],
                "initial_start" => ["required_if:type,individual|jdate:Y/m/d","nullable"],
                "initial_end" => ["required_if:type,individual|jdate:Y/m/d|jdate_after:{$request->input('initial_start')},Y/m/d","nullable"],
                "mobile" => "sometimes|nullable"
            ], [
                "contract_id.required" => "انتخاب سازمان و قرارداد الزامی می باشد",
                "contract_id.numeric" => "سازمان و قرارداد انتخاب شده نامعتبر است",
                "type.required" => "انتخاب نوع عملیات الزامی می باشد",
                "type.in" => "نوع عملیات مشخص نمی باشد",
                "employees.required_if" => "پرسنل بارگذاری نشده اند",
                "first_name.required_if" => "درج نام الزامی می باشد",
                "last_name.required_if" => "درج نام خانوادگی الزامی می باشد",
                "initial_start.required_if" => "انتخاب تاریخ شروع قرارداد الزامی می باشد",
                "initial_start.jdate" => "فرمت تاریخ شروع قرارداد صحیح نمی باشد",
                "initial_end.required_if" => "انتخاب تاریخ پایان قرارداد الزامی می باشد",
                "initial_end.jdate" => "فرمت تاریخ پایان صحیح نمی باشد",
                "initial_end.jdate_after" => "تاریخ پایان باید پس از تاریخ شروع قرارداد باشد",
                "national_code.required_if" => "درج کد ملی الزامی می باشد",
                "national_code.unique" => "کد ملی وارد شده تکراری می باشد",
            ]);
            switch ($request->type) {
                case "individual":
                {
                    $employee = Employee::query()->create([
                        "user_id" => Auth::id(),
                        "contract_id" => $request->contract_id,
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "national_code" =>$request->national_code,
                        "initial_start" => $this->Gregorian($request->initial_start),
                        "initial_end" => $this->Gregorian($request->initial_end),
                        "mobile" => $request->mobile,
                    ]);
                    if ($request->withDashboard == "true"){
                        User::query()->create([
                            "user_id" => Auth::id(),
                            "employee_id" => $employee->id,
                            "name" => $employee->name,
                            "username" => $employee->national_code,
                            "password" => Hash::make($employee->national_code),
                            "mobile" => $employee->mobile,
                            "is_user" => 1
                        ]);
                    }
                    $message = "پرسنل جدید با موفقیت ایجاد شد";
                    break;
                }
                case "group":
                {
                    $employees = json_decode($request->employees,true);
                    for($i = 0 ; $i < count($employees) ; $i++) {
                        $employees[$i]["contract_id"] = $request->contract_id;
                        $employees[$i]["user_id"] = Auth::id();
                        $employees[$i]["initial_start"] = $this->Gregorian($employees[$i]["initial_start"]);
                        $employees[$i]["initial_end"] = $this->Gregorian($employees[$i]["initial_end"]);
                        $new_employee = Employee::query()->create($employees[$i]);
                        if ($request->withDashboard == "true"){
                            User::query()->create([
                                "user_id" => Auth::id(),
                                "employee_id" => $new_employee->id,
                                "name" => $new_employee->name,
                                "username" => $new_employee->national_code,
                                "password" => Hash::make($new_employee->national_code),
                                "mobile" => $new_employee->mobile,
                                "is_user" => 1
                            ]);
                        }
                    }
                    $message = "کلیه پرسنل با موفقیت ایجاد گردید";
                    break;
                }
            }
            DB::commit();
            $response["result"] = "success";
            $response["message"] = $message;
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function delete_employee(Request $request): array
    {
        Gate::authorize('delete_item',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $employees = "";
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"]
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
            ]);
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->where("id","=",$employee_id)->get();
                    break;
                }
            }
            if ($employees->isNotEmpty()) {
                $employees->map(function ($employee) use ($request) {
                    $employee = Employee::query()->findOrFail($employee["id"]);
                    if ($request->delete_authentication == "true")
                        $employee->user_dashboard()->delete();
                    $employee->update(["user_id" => Auth::id()]);
                    $employee->delete();
                });
            }
            else
                throw ValidationException::withMessages(['employees' => 'پرسنلی جهت انجام عملیات وجود ندارد']);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function detach_employee(Request $request): array
    {
        //Gate::authorize('detach_employee',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $employees = "";
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "operation" => ["required",Rule::in(['attach','detach'])]
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "operation.required" => "نوع عملیات مشخص نمی باشد",
                "operation.in" => "نوع عملیات مشخص نمی باشد"
            ]);
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->where("id","=",$employee_id)->get();
                    break;
                }
            }
            if ($employees->isNotEmpty()) {
                $employees->map(function ($employee) use ($request) {
                    $employee = Employee::query()->findOrFail($employee["id"]);
                    $employee->update(["detached" => $request->input("operation") == 'detach' ? 1 : 0]);
                });
            }
            else
                throw ValidationException::withMessages(['employees' => 'پرسنلی جهت انجام عملیات وجود ندارد']);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function edit_item(Request $request): array
    {
        Gate::authorize('edit_item',"EmployeesManagement");
        try {
            $response = [];
            $data = json_decode($request->input("data"),true);
            $employee = Employee::query()->findOrFail($data["id"]);
            $employee->update($data);
            $response["result"] = "success";
            $response["message"] = "عملیات ذخیره سازی با موفقیت انجام شد";
            $response["data"] = $data;
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = $request->toArray();
            return $response;
        }
    }
    public function employee_status(Request $request): array
    {
        Gate::authorize('item_status',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $operation = $request->input("operation");
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "operation" => ["required",Rule::in(['lock','unlock'])],
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "operation.required" => "انتخاب نوع عملیات الزامی می باشد",
                "operation.in" => "نوع عملیات ارسال شده معتبر نمی باشد",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
            ]);
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    User::query()->whereHas("employee",function($query) use ($contract_id){
                        $query->where("employees.contract_id","=",$contract_id);
                    })->update(["inactive" => $operation == "lock" ? 1 : 0]);
                    break;
                }
                case "group":{
                    $employees = CustomGroup::query()->with("employees.employee")->findOrFail($request->input("group_id"));
                    $employees = $employees->employees->pluck("employee_id")->toArray();
                    User::query()->whereHas("employee",function($query) use ($employees){
                        $query->whereIn("id",$employees);
                    })->update(["inactive" => $operation == "lock" ? 1 : 0]);
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list){
                        $employees = [];
                        foreach ($employees_list as $employee)
                            $employees [] = $employee["id"];
                        User::query()->whereHas("employee",function($query) use ($employees){
                            $query->whereIn("id",$employees);
                        })->update(["inactive" => $operation == "lock" ? 1 : 0]);
                    }
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    User::query()->whereHas("employee",function($query) use ($employee_id){
                        $query->where("id","=",$employee_id);
                    })->update(["inactive" => $operation == "lock" ? 1 : 0]);
                    break;
                }
            }
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function employee_auth(Request $request): array
    {
        Gate::authorize('item_auth',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "password" => ["required_if:auth_type,custom"],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "auth_type" => ["required",Rule::in(['national_code','custom'])],
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "auth_type.required" => "انتخاب نوع اطلاعات برای بازنشانی الزامی می باشد",
                "auth_type.in" => "نوع اطلاعات ارسال شده معتبر نمی باشد",
                "password.required_if" => "درج گذرواژه الزامی می باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
            ]);
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $users = User::query()->with("employee")->whereHas("employee",function($query) use ($contract_id){
                        $query->where("employees.contract_id","=",$contract_id);
                    })->get();
                    $users->map(function ($user) use ($request){
                        $user->update([
                            "username" => $user->employee->national_code,
                            "password" => $request->has("password") ? Hash::make($request->input("password")) : Hash::make($user->employee->national_code),
                        ]);
                    });
                    break;
                }
                case "group":{
                    $employees = CustomGroup::query()->with("employees.employee")->findOrFail($request->input("group_id"));
                    $employees = $employees->employees->pluck("employee_id")->toArray();
                    $users = User::query()->whereHas("employee",function($query) use ($employees){
                        $query->whereIn("id",$employees);
                    })->get();
                    $users->map(function ($user) use ($request){
                        $user->update([
                            "username" => $user->employee->national_code,
                            "password" => $request->has("password") ? Hash::make($request->input("password")) : Hash::make($user->employee->national_code),
                        ]);
                    });
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list){
                        $employees = [];
                        foreach ($employees_list as $employee)
                            $employees [] = $employee["id"];
                        $users = User::query()->whereHas("employee",function($query) use ($employees){
                            $query->whereIn("id",$employees);
                        })->get();
                        $users->map(function ($user) use ($request){
                            $user->update([
                                "username" => $user->employee->national_code,
                                "password" => $request->has("password") ? Hash::make($request->input("password")) : Hash::make($user->employee->national_code),
                            ]);
                        });
                    }
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $users = User::query()->with("employee")->whereHas("employee",function($query) use ($employee_id){
                        $query->where("employees.id","=",$employee_id);
                    })->get();
                    $users->map(function ($user) use ($request){
                        $user->update([
                            "username" => $user->employee->national_code,
                            "password" => $request->has("password") ? Hash::make($request->input("password")) : Hash::make($user->employee->national_code),
                        ]);
                    });
                    break;
                }
            }
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function employee_refresh_data(Request $request): array
    {
        Gate::authorize('item_data_refresh',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "data" => ["required"],
                "title" => ["required"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "dashboard_lock" => ["required"],
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "data.required" => "انتخاب حداقل یک مورد از عناوین اطلاعات الزامی می باشد",
                "title.required" => "درج متن نمایش الزامی می باشد",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "dashboard_lock.required" => "فعال یا غیر فعال بودن داشبورد مشخص نمی باشد"
            ]);
            $data_items = [];
            $data = json_decode($request->input("data"),true);
            foreach ($data["files"] as $key => $value){
                if($value === true)
                    $data_items["files"][] = $key;
            }
            foreach ($data["texts"] as $key => $value){
                if($value === true)
                    $data_items["texts"][] = $key;
            }
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    $employees->map(function ($employee) use ($request,$data_items){
                        EmployeeDataRequest::query()->updateOrCreate(["employee_id" => $employee->id],[
                            "user_id" => Auth::id(),
                            "employee_id" => $employee->id,
                            "title" => $request->input("title"),
                            "data_items" => json_encode($data_items),
                            "lock_dashboard" => $request->input("dashboard_lock") == "true" ? 1 : 0
                        ]);
                    });
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    $employees->map(function ($employee) use ($request,$data_items){
                        EmployeeDataRequest::query()->updateOrCreate(["employee_id" => $employee->id],[
                            "user_id" => Auth::id(),
                            "employee_id" => $employee->id,
                            "title" => $request->input("title"),
                            "data_items" => json_encode($data_items),
                            "lock_dashboard" => $request->input("dashboard_lock") == "true" ? 1 : 0
                        ]);
                    });
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list){
                        foreach ($employees_list as $employee){
                            EmployeeDataRequest::query()->updateOrCreate(["employee_id" => $employee["id"]],[
                                "user_id" => Auth::id(),
                                "employee_id" => $employee["id"],
                                "title" => $request->input("title"),
                                "data_items" => json_encode($data_items),
                                "lock_dashboard" => $request->input("dashboard_lock") == "true" ? 1 : 0
                            ]);
                        }
                    }
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employee = Employee::query()->findOrFail($employee_id);
                    EmployeeDataRequest::query()->updateOrCreate(["employee_id" => $employee->id],[
                        "user_id" => Auth::id(),
                        "employee_id" => $employee->id,
                        "title" => $request->input("title"),
                        "data_items" => json_encode($data_items),
                        "lock_dashboard" => $request->input("dashboard_lock") == "true" ? 1 : 0
                    ]);
                    break;
                }
            }
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            return $response;
        }
    }
    public function employee_date_extension(Request $request): array
    {
        Gate::authorize('item_date_extension',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $employees = null;
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "operation" => ["required",Rule::in(['extend', 'initial'])],
                "extension_type" => ["required_if:reference,organization,group"],
                "start" => ["jdate:Y/m/d","required_if:extension_type,constant_value"],
                "end" => ["jdate:Y/m/d","jdate_after:{$request->input('start')},Y/m/d","required_if:extension_type,constant_value"],
                "appended_month" => ["required_if:extension_type,append_value"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "operation.required" => "انتخاب نوع عملیات الزامی می باشد",
                "operation.in" => "نوع عملیات معتبر نمی باشد",
                "extension_type.required_if" => "انتخاب نوع تمدید قرارداد الزامی می باشد",
                "start.required_if" => "انتخاب تاریخ شروع الزامی می باشد",
                "start.jdate" => "فرمت تاریخ شروع معتبر نمی باشد",
                "end.required_if" => "انتخاب پایان شروع الزامی می باشد",
                "end.jdate" => "فرمت تاریخ پایان معتبر نمی باشد",
                "end.jdate_after" => "تاریخ پایان باید بعد از تاریخ شروع انتخاب شود",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "appended_month.required_if" => "انتخاب تعداد ماه های افزایش الزامی می باشد"
            ]);
            switch ($request->input("reference")) {
                case "organization":
                {
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id", "=", $contract_id)->get();
                    break;
                }
                case "group":
                {
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id", $group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":
                {
                    $employees_list = json_decode($request->input("employees"), true);
                    $employee_ids = [];
                    if ($employees_list) {
                        foreach ($employees_list as $employee)
                            $employee_ids[] = $employee["id"];
                        $employees = Employee::query()->whereIn("id", $employee_ids)->get();
                        for ($i = 0 ; $i < count($employees->toArray()) ; $i++){
                            foreach ($employees_list as $emp){
                                if ($emp["id"] == $employees[$i]->id) {
                                    $employees[$i]["start"] = $emp["start"];
                                    $employees[$i]["end"] = $emp["end"];
                                    break;
                                }
                            }
                        }
                        $employees->map(function ($employee) use ($employees_list, $request) {
                            $start = $this->Gregorian($employee["start"]);
                            $end = $this->Gregorian($employee["end"]);
                            if ($request->input("operation") === "extend") {
                                ContractExtension::query()->where("employee_id","=",$employee->id)->update(["active" => 0]);
                                ContractExtension::query()->create([
                                    "user_id" => Auth::id(),
                                    "employee_id" => $employee->id,
                                    "start" => $start,
                                    "end" => $end,
                                    "active" => 1
                                ]);
                            }
                            if ($request->input("operation") == "initial")
                                Employee::query()->findOrFail($employee->id)->update([
                                    "initial_start" => $start,
                                    "initial_end" => $end
                                ]);
                        });
                    } else {
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employee = Employee::query()->findOrFail($employee_id);
                    $start = $this->Gregorian($request->input("start"));
                    $end = $this->Gregorian($request->input("end"));
                    if ($request->input("operation") === "extend") {
                        ContractExtension::query()->where("employee_id", "=", $employee->id)->update(["active" => 0]);
                        ContractExtension::query()->create([
                            "user_id" => Auth::id(),
                            "employee_id" => $employee->id,
                            "start" => $start,
                            "end" => $end,
                            "active" => 1
                        ]);
                    }
                    if ($request->input("operation") == "initial"){
                        $employee->update([
                            "initial_start" => $start,
                            "initial_end" => $end
                        ]);
                    }
                    break;
                }
            }
            if ($request->input("reference") == "organization" || $request->input("reference") == "group") {
                switch ($request->input("extension_type")) {
                    case "constant_value":
                    {
                        $start = $this->Gregorian($request->input("start"));
                        $end = $this->Gregorian($request->input("end"));
                        if ($request->input("operation") === "extend")
                            $employees->map(function ($employee) use ($start, $end) {
                                ContractExtension::query()->where("employee_id","=",$employee->id)->update(["active" => 0]);
                                ContractExtension::query()->create([
                                    "user_id" => Auth::id(),
                                    "employee_id" => $employee->id,
                                    "start" => $start,
                                    "end" => $end,
                                    "active" => 1
                                ]);
                            });
                        if ($request->input("operation") == "initial")
                            $employees->map(function ($employee) use ($start, $end) {
                                Employee::query()->findOrFail($employee->id)->update([
                                    "initial_start" => $start,
                                    "initial_end" => $end
                                ]);
                            });
                        break;
                    }
                    case "append_value":
                    {
                        $append_value = $request->input("appended_month");
                        if ($request->input("operation") === "extend")
                            $employees->map(function ($employee) use ($append_value) {
                                $start = Carbon::createFromTimestamp($employee->active_contract_date() != null ? $employee->active_contract_date()["start"] : $employee->initial_start);
                                $end = Carbon::createFromTimestamp($employee->active_contract_date() != null ? $employee->active_contract_date()["end"] : $employee->initial_end);
                                ContractExtension::query()->where("employee_id","=",$employee->id)->update(["active" => 0]);
                                ContractExtension::query()->create([
                                    "user_id" => Auth::id(),
                                    "employee_id" => $employee->id,
                                    "start" => $start->addMonths($append_value)->format("Y/m/d"),
                                    "end" => $end->addMonths($append_value)->format("Y/m/d"),
                                    "active" => 1
                                ]);
                            });
                        if ($request->input("operation") == "initial")
                            $employees->map(function ($employee) use ($append_value) {
                                $start = Carbon::createFromTimestamp($employee->initial_start);
                                $end = Carbon::createFromTimestamp($employee->initial_end);
                                Employee::query()->findOrFail($employee->id)->update([
                                    "initial_start" => $start->addMonths($append_value)->format("Y/m/d"),
                                    "initial_end" => $end->addMonths($append_value)->format("Y/m/d")
                                ]);
                            });
                        break;
                    }
                }
            }
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error){
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage().$error->getLine();
            return $response;
        }
    }
    public function employee_contract_conversion(Request $request): array
    {
        Gate::authorize('item_contract_conversion',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "target" => ["required"]
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "target.required" => "انتخاب قرارداد نهایی الزامی می باشد"
            ]);
            $target = $request->input("target");
            $employees = collect();
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->where("id","=",$employee_id)->get();
                    break;
                }
            }
            if ($employees->isNotEmpty()) {
                $employees->map(function ($employee) use ($target) {
                    if ($target != $employee->contract_id) {
                        ContractConversion::query()->create([
                            "user_id" => Auth::id(),
                            "employee_id" => $employee->id,
                            "contract_id" => $employee->contract_id
                        ]);
                        $employee->update(["contract_id" => $target]);
                    }
                });
            }
            else
                throw ValidationException::withMessages(['employees' => 'پرسنلی جهت انجام عملیات وجود ندارد']);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function employee_excel_list(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|array
    {
        Gate::authorize('item_excel_list',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required", Rule::in(['organization', 'group', 'custom'])],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "data" => "required"
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "data.required" => "انتخاب حداقل یک مورد از عناوین اطلاعات الزامی می باشد"
            ]);
            $employees = collect();
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->where("id","=",$employee_id)->get();
                    break;
                }
            }
            $titles = [];
            $data = json_decode($request->input("data"),true);
            $keywords = DbKeyword::all();
            foreach ($data as $key => $value){
                if($value === true){
                    $title = $keywords->where("data","=",$key)->first();
                    if ($title)
                        $titles[] = ["name" => $title->name, "data" => $key];
                }
            }
            return Excel::download(new ExportEmployeeCustomList($employees,$titles), "EmployeeCustomList.xlsx");
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function employee_send_sms(Request $request): array
    {
        Gate::authorize('send_sms',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "message" => "required"
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "message.required" => "درج متن پیام الزامی می باشد"
            ]);
            $employees = collect();
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->where("id","=",$employee_id)->get();
                    break;
                }
            }
            $sms_result = $this->send_sms(array_column($employees->toArray(),"mobile"),$request->input("message"));
            if ($sms_result){
                $response["result"] = "success";
                $response["message"] = "عملیات با موفقیت انجام شد";
            }
            else{
                $response["result"] = "fail";
                $response["message"] = "ارسال پیامک با خطا مواجه شد";
            }
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function employee_send_ticket(Request $request): array
    {
        Gate::authorize('send_ticket',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required",Rule::in(['organization','group','custom','individual'])],
                "employee_id" => ["required_if:reference,individual"],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employees" => ["required_if:reference,custom"],
                "subject" => "required",
                "message" => "required"
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "employee_id.required_if" => "پرسنلی از جدول انتخاب نشده است",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "subject.required" => "درج عنوان پیام الزامی می باشد",
                "message.required" => "درج متن پیام الزامی می باشد"
            ]);
            $employees = collect();
            switch ($request->input("reference")){
                case "organization":{
                    $contract_id = $request->input("contract_id");
                    $employees = Employee::query()->where("contract_id","=",$contract_id)->get();
                    break;
                }
                case "group":{
                    $group = CustomGroup::query()->with("employees")->findOrFail($request->input("group_id"));
                    $employees = Employee::query()->whereIn("id",$group->employees->pluck("employee_id")->toArray())->get();
                    break;
                }
                case "custom":{
                    $employees_list = json_decode($request->input("employees"),true);
                    if ($employees_list)
                        $employees = Employee::query()->whereIn("id",array_column($employees_list,"id"))->get();
                    else{
                        throw ValidationException::withMessages(['employees' => 'لیست پرسنلی جهت انجام عملیات وجود ندارد']);
                    }
                    break;
                }
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->where("id","=",$employee_id)->get();
                    break;
                }
            }
            $subject = $request->input("subject");
            $message = $request->input("message");
            $room_id = TicketRoom::query()->create(["subject" => $subject,"user_id" => Auth::id()]);
            $filename = $request->hasFile("attachment") ? $request->file("attachment")->hashName() : null;
            if($filename)
                Storage::disk("ticket_attachments")->put($room_id->id,$request->file("attachment"));
            $employees->map(function ($employee) use ($filename,$room_id,$message){
                $employee->tickets()->create([
                    "expert_id" => Auth()->id(),
                    "room_id" => $room_id->id,
                    "sender" => "expert",
                    "message" => $message,
                    "attachment" => $filename
                ]);
            });
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "ارسال تیکت پشتیبانی با موفقیت انجام شد";
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function requests_history(Request $request): array
    {
        Gate::authorize('requests_history',"EmployeesManagement");
        try {
            $response = [];
            $request->validate([
                "employee_id" => "required"], ["employee_id.required" => "پرسنلی انتخاب نشده است"]);
            $employee_id = $request->input("employee_id");
            $response["result"] = "success";
            $response["automations"] = Automation::query()->with(["automationable","user","employee","contract","signs.user.role","comments.user.role"])->where("employee_id","=",$employee_id)->get()->toArray();
            return $response;
        }
        catch (Throwable $error) {
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function clear_debt(Request $request): array
    {
        try {
            $response = [];
            $request->validate([
                "automation_id" => "required"], ["automation_id.required" => "درخواستی انتخاب نشده است"]);
            $automation_id = $request->input("automation_id");
            $automation = Automation::query()->findOrFail($automation_id);
            $automation->automationable()->update(["inactive" => 1]);
            $response["result"] = "success";
            $response["automations"] = Automation::query()->with(["automationable","user","employee","contract","signs.user.role","comments.user.role"])->where("employee_id","=",$automation->employee_id)->get()->toArray();
            return $response;
        }
        catch (Throwable $error) {
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function request_preview($id){
        //Gate::authorize('requests_history_print',"EmployeesManagement");
        try {
            $automation = Automation::query()->with(["user","automationable","employee.automations.user","employee.automations.signs","employee.automations.automationable","signs.user.role","comments.user.role"])->findOrFail($id);
            $qrCode = new QrCode(route("Validation.direct",["i_number" => $automation->automationable->i_number]));
            $output = new Output\Png();
            $pdf = PDF::loadView("layouts.pdf.{$automation->application_class}", [
                "application" => $automation,
                "background" => base64_encode(file_get_contents(public_path("images/A4.jpg"))),
                "qrCode" => base64_encode($output->output($qrCode, 100, [255, 255, 255], [0, 0, 0])),
                "company_information" => CompanyInformation::query()->first(),
                "logo" => base64_encode(file_get_contents(public_path("/images/logo.png"))),
                "number" => $automation->automationable->i_number,
                "sign" => false
            ], [], [
                'format' => "A4-P"
            ]);
            return response()->make($pdf->stream("request.pdf"),200,[
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="request.pdf"'
            ]);
        }
        catch (Throwable $error) {
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function history(Request $request): array
    {
        Gate::authorize('history',"EmployeesManagement");
        try {
            $response = [];
            $request->validate([
                "employee_id" => "required"], ["employee_id.required" => "پرسنلی انتخاب نشده است"]);
            $employee_id = $request->input("employee_id");
            $employee = Employee::query()->with(["contract_extensions.user","contract_conversions.contract","contract_conversions.user"])
                ->findOrFail($employee_id)->toArray();
            $response["result"] = "success";
            $response["history"] = ["extensions" => $employee["contract_extensions"], "conversions" => $employee["contract_conversions"]];
            return $response;
        }
        catch (Throwable $error) {
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function employee_batch_application(Request $request): array
    {
        Gate::authorize('item_batch_application',"EmployeesManagement");
        try {
            DB::beginTransaction();
            $response = [];
            $request->validate([
                "reference" => ["required", Rule::in(['organization', 'group', 'custom','individual'])],
                "contract_id" => ["required_if:reference,organization"],
                "group_id" => ["required_if:reference,group"],
                "employee_id" => ["required_if:reference,individual"],
                "employees" => ["required_if:reference,custom"],
                "application_type" => ["required"],
                "operation_type" => ["required" , Rule::in(['view','save'])],
            ], [
                "reference.required" => "انتخاب مرجع عملیات الزامی می باشد",
                "reference.in" => "مرجع عملیات ارسال شده معتبر نمی باشد",
                "contract_id.required_if" => "سازمان و قراردادی انتخاب نشده است",
                "group_id.required_if" => "گروه سفارشی انتخاب نشده است",
                "employees.required_if" => "پرسنلی بارگذاری نشده است",
                "employee_id.required_if" => "پرسنلی بارگذاری نشده است",
                "application_type.required" => "انتخاب نوع درخواست الزامی می باشد",
                "operation_type.required" => "انتخاب نوع عملیات الزامی می باشد",
                "operation_type.in" => "نوع عملیات ارسال شده معتبر نمی باشد",
            ]);
            $employees = collect();
            $type = $request->input("operation_type");
            $application_type = $request->input("application_type");
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
                case "individual":{
                    $employee_id = $request->input("employee_id");
                    $employees = Employee::query()->with(["contract.organization"])->where("id","=",$employee_id)->get();
                    break;
                }
            }
            if ($employees->isNotEmpty()) {
                $company_information = CompanyInformation::query()->with("ceo")->first();
                $folder = Str::random(8);
                Storage::disk("temporarily")->makeDirectory($folder);
                $form = ApplicationForm::Application($request->input("application_type"));
                $application = $form->application_class;
                $application = $application == "PAF" ? "PAFBatch" : $application;
                switch ($type){
                    case "view":{
                        $employees->map(function ($employee) use ($folder,$company_information,$application,$form) {
                            $pdf = PDF::loadView("layouts.pdf.{$application}", [
                                "test" => true,
                                "employee" => $employee,
                                "application" => $form,
                                "active_contract" => $employee->contract->organization->name,
                                "active_contract_date" => $employee->active_contract_date(),
                                "payslip" => EmployeePaySlip::Last($employee->id),
                                "logo" => base64_encode(file_get_contents(public_path("/images/logo.png"))),
                                "number" => " ",
                                "sign" => "",
                                "background" => base64_encode(file_get_contents(public_path("images/A4.jpg"))),
                                "company_information" => $company_information
                            ], [], [
                                'format' => "A4-P"
                            ]);
                            $file = Str::random(8);
                            $pdf->save(Storage::disk("temporarily")->path("$folder/$file.pdf"));
                        });
                        $merge = PDFMerger::init();
                        foreach (Storage::disk("temporarily")->files($folder) as $file) {
                            $merge->addPDF(Storage::disk("temporarily")->path("$file"), 'all');
                        }
                        $merge->merge();
                        $merge->save(Storage::disk("temporarily")->path("{$folder}/{$folder}.pdf"));
                        foreach (Storage::disk("temporarily")->files($folder) as $file) {
                            if ($file != "$folder/$folder.pdf")
                                Storage::disk("temporarily")->delete($file);
                        }
                        $response["data"]["view"] = route("docs.application_download",["path" => "$folder@$folder.pdf"]);
                        break;
                    }
                    case "save":{
                        $automation = ApplicationForm::MakeAutomation($application_type);
                        $printable = 1;//count(json_decode($automation["details"],true)) == 1;
                        $loan_amount = $request->has("loan_amount") ? Str::replace(",","",$request->input("loan_amount")) : 0;
                        $recipient = $request->has("recipient") ? $request->input("recipient") : null;
                        $borrower = $request->has("borrower") ? $request->input("borrower") : null;
                        $comment = $request->input("comment");
                        $employees->map(function ($employee) use ($application,$folder,$company_information,$application_type,$recipient,$loan_amount,$borrower,$printable,$automation,$comment) {
                            $class = '';
                            $data = json_encode([
                                "active_contract" => [
                                    "organization_id" => $employee->contract->organization->id,
                                    "organization_name" => $employee->contract->organization->name,
                                    "contract_id" => $employee->contract->id,
                                    "contract_name" => $employee->contract->name
                                ],
                                "payslip" => EmployeePaySlip::Last($employee->id),
                                "active_contract_date" => $employee->active_contract_date()
                            ],JSON_UNESCAPED_UNICODE);
                            $pre_number = implode('',Str::matchAll("/[A-Z]+/",$application_type)->toArray());
                            switch ($application_type){
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
                            $form = $class::query()->create([
                                "user_id" => Auth::id(),
                                "employee_id" => $employee->id,
                                "recipient" => $recipient,
                                "borrower" => $borrower,
                                "loan_amount" => $loan_amount,
                                "is_accepted" => $printable ? 1 : 0,
                                "i_number" => $number,
                                "data" => $data,
                            ]);
                            $created_automation = $form->automation()->create([
                                "employee_id" => $employee->id,
                                "contract_id" => $employee->contract_id,
                                "current_role_id" => $automation["current_role_id"],
                                "current_priority" => $automation["current_priority"],
                                "user_id" => Auth::id(),
                                "flow" => $automation["details"],
                                "is_read" => $printable ? 1 : 0,
                                "is_finished" => $printable ? 1 : 0,
                                "editable" => 0,
                                "comment" => $comment
                            ]);
                            $created_automation->automate("forward","");
                            DB::commit();
                            if ($printable){
                                $final_application = Automation::with(["employee", "automationable"])->findOrFail($created_automation->id);
                                $qrCode = new QrCode(route("Validation.direct",["i_number" => $form->i_number]));
                                $output = new Output\Png();
                                $pdf = PDF::loadView("layouts.pdf.{$application}", [
                                    "application" => $final_application,
                                    "background" => base64_encode(file_get_contents(public_path("images/A4.jpg"))),
                                    "logo" => base64_encode(file_get_contents(public_path("/images/logo.png"))),
                                    "number" => preg_replace("/[^0-9]/", "", $form->i_number ),
                                    "qrCode" => base64_encode($output->output($qrCode, 100, [255, 255, 255], [0, 0, 0])),
                                    "company_information" => $company_information,
                                    "sign" => ["role" => $company_information->ceo_title,"name" => $company_information->ceo->name,"sign" => $company_information->ceo->GetSign()]
                                ], [], [
                                    'format' => "A4-P"
                                ]);
                                $file = Str::random(8);
                                $pdf->save(Storage::disk("temporarily")->path("$folder/$file.pdf"));
                            }
                            $merge = PDFMerger::init();
                            foreach (Storage::disk("temporarily")->files($folder) as $file) {
                                $merge->addPDF(Storage::disk("temporarily")->path("$file"), 'all');
                            }
                            $merge->merge();
                            $merge->save(Storage::disk("temporarily")->path("{$folder}/{$folder}.pdf"));
                            foreach (Storage::disk("temporarily")->files($folder) as $file) {
                                if ($file != "$folder/$folder.pdf")
                                    Storage::disk("temporarily")->delete($file);
                            }
                        });
                        $response["data"]["view"] = route("docs.application_download",["path" => "$folder@$folder.pdf"]);
                        break;
                    }
                }
            }
            else
                throw ValidationException::withMessages(['employees' => 'پرسنلی جهت انجام عملیات وجود ندارد']);
            DB::commit();
            $response["result"] = "success";
            $response["message"] = "عملیات با موفقیت انجام شد";
            $response["data"]["contracts"] = $this->allowed_contracts()->toArray();
            $response["data"]["groups"] = CustomGroup::with(["employees.employee.user","employees.employee.contract"])->whereHas("user",function ($query){
                $query->where("id","=",Auth::id());
            })->get()->toArray();
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            $response["data"] = [];
            return $response;
        }
    }
    public function get_deleted_employees(): array
    {
        Gate::authorize('get_deleted_employees',"EmployeesManagement");
        try {
            $response["result"] = "success";
            $response["message"] = "اطلاعات با موفقیت دریافت شد";
            $response["deleted_employees"] = Employee::onlyTrashed()->with(["contract.organization","registrant_user"])
                ->whereIn("contract_id",Contract::GetPermitted())->get()->toArray();
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
    public function recover_employee(Request $request): array
    {
        Gate::authorize('recover_employee',"EmployeesManagement");
        try {
            $request->validate([
                "employee_id" => "required",
            ], [
                "employee_id.required" => "پرسنلی انتخاب نشده است",
            ]);
            $employee = Employee::onlyTrashed()->findOrFail($request->input("employee_id"));
            $employee->restore();
            $employee->user()->withTrashed()->updateOrCreate(["employee_id" => $employee->id],[
                'user_id' => Auth::id(),
                'name' => $employee->name,
                'gender' => $employee->gender,
                'username' => $employee->national_code,
                'password' => Hash::make($employee->national_code),
                'mobile' => $employee->mobile,
                'is_super_user' => 0,
                'is_admin' => 0,
                'is_staff' => 0,
                'is_user' => 1,
                'inactive' => 0,
                'deleted_at' => null
            ]);
            $response["result"] = "success";
            $response["message"] = "اطلاعات با موفقیت دریافت شد";
            $response["deleted_employees"] = Employee::onlyTrashed()->with(["contract.organization","registrant_user"])
                ->whereIn("contract_id",Contract::GetPermitted())->get()->toArray();
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            $response["data"] = [];
            return $response;
        }
    }
    public function employee_get_tickets(Request $request): array
    {
        Gate::authorize('get_tickets',"EmployeesManagement");
        try {
            $request->validate([
                "id" => "required",
            ], [
                "id" => "پرسنلی انتخاب نشده است",
            ]);
            $response["result"] = "success";
            $response["message"] = "اطلاعات با موفقیت دریافت شد";
            $response["AllTickets"] = TicketRoom::chats($request->input("id"))->toArray();
            return $response;
        }
        catch (Throwable $error) {
            DB::rollBack();
            $response["result"] = "fail";
            $response["message"] = $error->getMessage() . $error->getLine();
            return $response;
        }
    }
}
