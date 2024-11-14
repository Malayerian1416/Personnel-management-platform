<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EmployeesRecruitingReloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["employees" => "string", "send_sms_permission" => "string", "sms_text" => "string", "db_titles" => "string", "doc_titles" => "string"])] public function rules(): array
    {
        return [
            "employees" => "required",
            "send_sms_permission" => "sometimes|nullable",
            "sms_text" => "required_with:send_sms_permission",
            "db_titles" => "required_without:doc_titles",
            "doc_titles" => "required_without:db_titles",
        ];
    }

    #[ArrayShape(["employees.required" => "string", "sms_text.required_with" => "string", "db_titles.required_without" => "string", "doc_titles.required_without" => "string"])] public function messages(): array
    {
        return [
            "employees.required" => "برای انجام عملیات، پرسنلی انتخاب نشده است",
            "sms_text.required_with" => "در صورت انتخاب گزینه ارسال پیامک، متن آن باید ارسال گردد",
            "db_titles.required_without" => "حداقل یک مورد از عناوین اطلاعات و یا مدارک باید انتخاب گردد",
            "doc_titles.required_without" => "حداقل یک مورد از عناوین اطلاعات و یا مدارک باید انتخاب گردد"
        ];
    }
}
