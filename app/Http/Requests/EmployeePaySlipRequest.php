<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EmployeePaySlipRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "payslip_employees" => "required",
            "year" => "required",
            "month" => "required"
        ];
    }

    #[ArrayShape(["payslip_employees.required" => "string", "year.required" => "string", "month.required" => "string"])] public function messages()
    {
        return [
            "payslip_employees.required" => "اطلاعات فیش حقوقی پرسنل یافت نشد",
            "year.required" => "انتخاب سال فیش حقوقی الزامی است",
            "month.required" => "انتخاب ماه فیش حقوقی الزامی است"
        ];
    }
}
