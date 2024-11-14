<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class PaySlipTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["contract_id" => "string", "excel_columns" => "string", "national_code_index" => "string"])] public function rules(): array
    {
        return [
            "contract_id" => "required",
            "excel_columns" => "required",
            "national_code_index" => "required"
        ];
    }
    #[ArrayShape(["contract_id.required" => "string", "excel_columns.required" => "string", "national_code_index.required" => "string"])] public function messages(): array
    {
        return [
            "contract_id.required" => "انتخاب قرارداد الزامی می باشد",
            "excel_columns.required" => "ایجاد حداقل یک ستون الزامی می باشد",
            "national_code_index.required" => "انتخاب ستون مربوط به کد ملی پرسنل الزامی می باشد"
        ];
    }
}
