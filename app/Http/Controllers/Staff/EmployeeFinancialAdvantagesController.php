<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ExportEmployeeAdvantages;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeFinancialAdvantageRequest;
use App\Imports\ImportEmployeeAdvantages;
use App\Models\Employee;
use App\Models\EmployeeFinancialAdvantage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Throwable;

class EmployeeFinancialAdvantagesController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"EmployeeFinancialAdvantages");
        try {
            return view("staff.employee_financial_advantages", [
                "organizations" => $this->allowed_contracts("tree"),
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
                "contract_id" => "required","effective_year" => "required"],
                [
                    "contract_id.required" => "انتخاب قرارداد الزامی می باشد",
                    "effective_year.required" => "انتخاب سال الزامی می باشد",
                ]
            );
            $employees = EmployeeFinancialAdvantage::BatchAdvantages($request->input("contract_id"),$request->input("effective_year"));
            return redirect()->route("EmployeeFinancialAdvantages.index")->with(["employees" => $employees,"query" => true]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function get_employees(Request $request): array
    {
        try {
            $request->validate(["contract_id" => "required"],["contract_id.required" => "انتخاب قرارداد الزامی می باشد"]);
            $contract_id = $request->input("contract_id");
            return [
                "result" => "success",
                "employees" => Employee::query()->where("contract_id","=",$contract_id)->get()
            ];
        }
        catch (Throwable $error){
            return [
                "result" => "fail",
                "message" => $error->getMessage()
            ];
        }
    }
    public function store(EmployeeFinancialAdvantageRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"EmployeeFinancialAdvantages");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $employees = json_decode($validated["employee_advantages"],true);
            $advantages = json_decode($validated["advantage_columns"],true);
            foreach ($employees as $employee){
                $all_advantage = [];
                foreach ($advantages as $key => $value)
                    $all_advantage[] = ["title" => $value,"value" => $employee["advantages"][$key]["value"]];
                EmployeeFinancialAdvantage::query()->updateOrCreate(["employee_id" => $employee["id"], "effective_year" => $validated["year"]],[
                    "user_id" => Auth::id(),
                    "daily_wage" => $employee["daily_wage"],
                    "prior_service" => $employee["prior_service"],
                    "working_days" => $employee["working_days"],
                    "occupational_group" => $employee["occupational_group"],
                    "count_of_children" => $employee["count_of_children"],
                    "advantages" => json_encode($all_advantage,JSON_UNESCAPED_UNICODE)
                ]);
                Employee::query()->findOrFail($employee["id"])->update(["included_children_count" => $employee["count_of_children"]]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function storeSolo(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"EmployeeFinancialAdvantages");
        try {
            DB::beginTransaction();
            $request->validate([
                "employee_id" => "required",
                "daily_wage" => "required",
                "prior_service" => "required",
                "working_days" => "required",
                "occupational_group" => "required",
                "count_of_children" => "required",
                "effective_year" => "required"
            ],[
                "employee_id.required" => "انتخاب پرسنل الزامی می باشد",
                "daily_wage.required" => "درج دستمزد روزانه الزامی می باشد",
                "prior_service.required" => "درج پایه سنوات الزامی می باشد",
                "working_days.required" => "درج روزهای کارکرد الزامی می باشد",
                "occupational_group.required" => "درج گروه شغلی الزامی می باشد",
                "count_of_children.required" => "درج تعداد فرزندان تحت تکفل الزامی می باشد",
                "effective_year.required" => "انتخاب سال مؤثر الزامی می باشد"
            ]);
            $advantages = json_decode($request->input("advantage_columns"),true);
            $all_advantage = [];
            foreach ($advantages as $advantage)
                $all_advantage[] = ["title" => $advantage["title"],"value" => Str::replace(",","",$advantage["value"])];
            EmployeeFinancialAdvantage::query()->updateOrCreate(["employee_id" => $request->input("employee_id"), "effective_year" => $request->input("effective_year")],[
                "user_id" => Auth::id(),
                "daily_wage" => Str::replace(",","",$request->input("daily_wage")),
                "prior_service" => Str::replace(",","",$request->input("prior_service")),
                "working_days" => $request->input("working_days"),
                "occupational_group" => $request->input("occupational_group"),
                "count_of_children" => $request->input("count_of_children"),
                "advantages" => json_encode($all_advantage,JSON_UNESCAPED_UNICODE)
            ]);
            Employee::query()->findOrFail($request->input("employee_id"))->update(["included_children_count" => $request->input("count_of_children")]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"EmployeeFinancialAdvantages");
        try {
            $financial_info = EmployeeFinancialAdvantage::query()->with("employee")->findOrFail($id);
            return view("staff.edit_employee_financial_advantages",[
                "organizations" => $this->allowed_contracts("tree"),
                "financial_info" => $financial_info
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(EmployeeFinancialAdvantageRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"EmployeeFinancialAdvantages");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $employee_advantages = EmployeeFinancialAdvantage::query()->findOrFail($id);
            $advantages = json_decode($request->input("advantage_columns"),true);
            $all_advantage = [];
            foreach ($advantages as $advantage)
                $all_advantage[] = ["title" => $advantage["title"],"value" => Str::replace(",","",$advantage["value"])];
            $employee_advantages->update([
                "user_id" => Auth::id(),
                "daily_wage" => Str::replace(",","",$validated["daily_wage"]),
                "prior_service" => Str::replace(",","",$validated["prior_service"]),
                "working_days" => $validated["working_days"],
                "occupational_group" => $validated["occupational_group"],
                "count_of_children" => $validated["count_of_children"],
                "advantages" => json_encode($all_advantage,JSON_UNESCAPED_UNICODE)
            ]);
            Employee::query()->findOrFail($employee_advantages->employee_id)->update(["included_children_count" => $validated["count_of_children"]]);
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
        Gate::authorize('delete',"EmployeeFinancialAdvantages");
        try {
            DB::beginTransaction();
            EmployeeFinancialAdvantage::query()->findOrFail($id)->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroyAll(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"EmployeeFinancialAdvantages");
        try {
            DB::beginTransaction();
            $contract_id = $request->input("contract_id");
            EmployeeFinancialAdvantage::query()->whereHas("employee",function ($query) use($contract_id){
                $query->where("employees.contract_id","=",$contract_id);
            })->where("effective_year","=",$request->input("effective_year"))->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function excel_download(): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            return Excel::download(new ExportEmployeeAdvantages, 'EmployeeAdvantages.xlsx');
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function excel_upload(Request $request): array
    {
        try {
            HeadingRowFormatter::default('none');
            $import_errors = [];
            $response = [];
            $flag = 'success';
            $import = new ImportEmployeeAdvantages($request->input("contract_id"));
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
                $message = "عملیات ناموفق! لطفا به قسمت خطای بارگذاری مراجعه نمایید";
            }
            elseif (count($import_errors) > 0) {
                $flag = "warning";
                $message = "عملیات به طور کامل انجام نشد! لطفا به قسمت خطای بارگذاری مراجعه نمایید";
            }
            else
                $message = "عملیات با موفقیت انجام شد";
            $advantage_columns = (new HeadingRowImport)->toArray($request->file("excel_file"))[0][0];
            for($i = 0 ; $i < 6 ; $i++)
                unset($advantage_columns[$i]);
            $response["result"] = $flag;
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"]["results"] = $import->getResult();
            $response["data"]["advantage_columns"] = array_values($advantage_columns);
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage().$error->getFile()." ({$error->getLine()})";
            return $response;
        }
    }
}
