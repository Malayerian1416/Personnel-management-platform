<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ExportEmployeeAdvantages;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeFinancialAdvantageRequest;
use App\Imports\ImportEmployeeAdvantages;
use App\Models\Employee;
use App\Models\EmployeeFinancialAdvantage;
use App\Models\EmployeePaySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Throwable;

class PersonnelAppointmentFormController extends Controller
{

    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"PersonnelAppointmentForms");
        try {
            $employees = [];
            if ($request->has("contract_id") && $request->has("effective_year")){
                $employees = EmployeeFinancialAdvantage::BatchAdvantages($request->input("contract_id"),$request->input("effective_year"));
            }
            return view("staff.employee_financial_advantages", [
                "organizations" => $this->allowed_contracts("tree"),
                "persian_month" => $this->persian_month(),
                "employees" => $employees
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(EmployeeFinancialAdvantageRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"PersonnelAppointmentForms");
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

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"PersonnelAppointmentForms");
        try {
            $financial_info = EmployeeFinancialAdvantage::query()->with("employee")->findOrFail($id);
            return view("staff.edit_employee_financial_advantage",["financial_info" => $financial_info]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(EmployeeFinancialAdvantageRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"PersonnelAppointmentForms");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $employees = json_decode($validated["employee_advantages"],true);
            $advantages = json_decode($validated["advantage_columns"],true);
            $all_advantage = [];
            foreach ($advantages as $key => $value)
                $all_advantage[] = ["title" => $value,"value" => $employees["advantages"][$key]["value"]];
            EmployeeFinancialAdvantage::query()->updateOrCreate(["employee_id" => $employees["id"], "effective_year" => $validated["year"]],[
                "user_id" => Auth::id(),
                "daily_wage" => $employees["daily_wage"],
                "prior_service" => $employees["prior_service"],
                "working_days" => $employees["working_days"],
                "occupational_group" => $employees["occupational_group"],
                "count_of_children" => $employees["count_of_children"],
                "advantages" => json_encode($all_advantage,JSON_UNESCAPED_UNICODE)
            ]);
            Employee::query()->findOrFail($employees["id"])->update(["included_children_count" => $employees["count_of_children"]]);
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

    public function destroyAll($contract_id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            EmployeeFinancialAdvantage::query()->whereHas("employee",function ($query) use($contract_id){
                $query->where("employees.contract_id","=",$contract_id);
            })->delete();
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
                $message = "عملیات ناموفق! لطفا به قسمت خطای بارگذاری مرجعه نمایید";
            }
            elseif (count($import_errors) > 0) {
                $flag = "warning";
                $message = "عملیات به طور کامل انجام نشد! لطفا به قسمت خطای بارگذاری مرجعه نمایید";
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
