<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EmployeesRecruitingRefuseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["employees" => "string", "send_sms_permission" => "string", "sms_text" => "string","delete_employees" => "string"])] public function rules(): array
    {
        return [
            "employees" => "required",
            "send_sms_permission" => "sometimes|nullable",
            "sms_text" => "required_with:send_sms_permission",
            "delete_employees" => "sometimes|nullable"
        ];
    }

    #[ArrayShape(["sms_text.required_with" => "string","employees.required" => "string"])] public function messages(): array
    {
        return [
            "employees.required" => "برای انجام عملیات، پرسنلی انتخاب نشده است",
            "sms_text.required_with" => "در صورت انتخاب گزینه ارسال پیامک، متن آن باید ارسال گردد"
        ];
    }
}
