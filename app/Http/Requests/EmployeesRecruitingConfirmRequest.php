<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class EmployeesRecruitingConfirmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "employees" => "required",
            "start_date" => "required|jdate:Y/m/d",
            "end_date" => "required|jdate:Y/m/d|jdate_after:{$this->input('start_date')},Y/m/d",
            "send_sms_permission" => "sometimes|nullable",
            "sms_text" => "required_with:send_sms_permission"
        ];
    }

    public function messages(): array
    {
        return [
            "employees.required" => "برای انجام عملیات ثبت نام، حداقل یک پرسنل باید انتخاب گردد",
            "start_date.required" => "انتخاب تاریخ شروع قرارداد الزامی است",
            "start_date.jdate" => "فرمت تاریخ شروع قرارداد صحیح نمی باشد",
            "end_date.required" => "انتخاب تاریخ پایان قرارداد الزامی است",
            "end_date.jdate" => "فرمت تاریخ پایان قرارداد صحیح نمی باشد",
            "end_date.jdate_after" => "تاریخ پایان قرارداد باید بعد از تاریخ شروع قرارداد انتخاب گردد",
            "sms_text.required_with" => "در صورت انتخاب گزینه ارسال پیامک، متن آن باید ارسال گردد"
        ];
    }
}
