<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\EmployeePaySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Throwable;

class UserPaySlipController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $payslipCount = Env::get("PAYSLIP_COUNT",3);
            $payslips = EmployeePaySlip::query()->with("employee")
                ->where("employee_id","=",Auth::user()->employee->id)->orderBy("date_serial","desc")->take($payslipCount)->get();
            return view("user.payslips",["payslips" => $payslips]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function published(Request $request): array|\Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate(["payslip_id" => "required"],["payslip_id.required" => "فیش حقوقی با این شماره وجود ندارد"]);
            $payslip = EmployeePaySlip::query()->with("employee.contract.organization")->findOrFail($request->input("payslip_id"));
            return [
                "result" => "success",
                "published" => $payslip->published()
            ];
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function PdfDownload($id): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        try {
            $payslip = EmployeePaySlip::query()->with("employee.contract.organization")->findOrFail($id);
            $pdf = PDF::loadView('layouts.pdf.payslip',["payslip" => $payslip->published(),"logo" => base64_encode(file_get_contents(public_path("/images/logo.png")))],[], [
                'format' => "A4-L"
            ]);
            ;
            return $pdf->download("payslip-{$payslip->persian_year}{$payslip->persian_month}{$payslip->employee->national_code}.pdf");
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage().$error->getFile().$error->getLine()]);
        }
    }
}
