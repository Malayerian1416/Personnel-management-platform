<?php

namespace App\Http\Controllers;

use App\Models\BackgroundCheckApplication;
use App\Models\EmployeePaySlip;
use App\Models\EmploymentCertificateApplication;
use App\Models\LoanPaymentConfirmationApplication;
use App\Models\OccupationalMedicineApplication;
use Illuminate\Http\Request;
use Throwable;

class ValidationController extends Controller
{
    public function direct($i_number): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $category = preg_filter("/[^A-Z]+/","",$i_number);
        $type = "request";
        $salary = null;
        switch ($category){
            case "LPCA":{
                $class = LoanPaymentConfirmationApplication::class;
                break;
            }
            case "ECA":{
                $class = EmploymentCertificateApplication::class;
                break;
            }
            case "OMA":{
                $class = OccupationalMedicineApplication::class;
                break;
            }
            case "BCA":{
                $class = BackgroundCheckApplication::class;
                break;
            }
            default: {
                $class = EmployeePaySlip::class;
                $type = "payslip";
            }
        }
        if ($type == "request") {
            $application = $class::query()->with(["employee", "automation.automationable"])->where("i_number", "=", $i_number)->first();
            $status = $application != null && $application->automation->expiration_date == "remain" ? "approved" : "rejected";
        }
        else {
            $application = $class::query()->with("employee.contract.organization")->where("i_number", "=", $i_number)->first();
            $status = $application != null ? "approved" : "rejected";
            if ($status == "approved")
                $salary = $application->published();
        }
        return view("validation_result",[
            "status" => $status,
            "application" => $application,
            "type" => $type,
            "salary" => $salary
        ]);
    }
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            return view("validation");
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function check(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $request->validate(["i_number" => "required"],["i_number.required" => "لطفا شناسه یکتای مندرج برروی نامه را وارد نمایید"]);
        $i_number = $request->input("i_number");
        $category = preg_filter("/[^A-Z]+/","",$i_number);
        $type = "request";
        $salary = null;
        switch ($category){
            case "LPCA":{
                $class = LoanPaymentConfirmationApplication::class;
                break;
            }
            case "ECA":{
                $class = EmploymentCertificateApplication::class;
                break;
            }
            case "OMA":{
                $class = OccupationalMedicineApplication::class;
                break;
            }
            case "BCA":{
                $class = BackgroundCheckApplication::class;
                break;
            }
            default: {
                $class = EmployeePaySlip::class;
                $type = "payslip";
            }
        }
        if ($type == "request") {
            $application = $class::query()->with(["employee", "automation.automationable"])->where("i_number", "=", $i_number)->first();
            $status = $application != null && $application->automation->expiration_date == "remain" ? "approved" : "rejected";
        }
        else {
            $application = $class::query()->with("employee.contract.organization")->where("i_number", "=", $i_number)->first();
            $status = $application != null ? "approved" : "rejected";
            if ($status == "approved")
                $salary = $application->published();
        }
        return view("validation_result",[
            "status" => $status,
            "application" => $application,
            "type" => $type,
            "salary" => $salary
        ]);
    }
}
