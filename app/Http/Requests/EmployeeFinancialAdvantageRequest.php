<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EmployeeFinancialAdvantageRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->method() == "PUT"){
            return [
                "daily_wage" => "required",
                "prior_service" => "required",
                "working_days" => "required",
                "occupational_group" => "required",
                "count_of_children" => "required",
                "effective_year" => "required"
            ];
        }
        else {
            return [
                "employee_advantages" => "required",
                "advantage_columns" => "required",
                "year" => "required"
            ];
        }
    }

    #[ArrayShape(["employee_advantages.required" => "string", "advantage_columns.required" => "string", "year.required" => "string", "daily_wage.required" => "string", "prior_service.required" => "string", "working_days.required" => "string", "occupational_group.required" => "string", "count_of_children.required" => "string", "effective_year.required" => "string"])] public function messages(): array
    {
        return [
            "employee_advantages.required" => "لیست پرسنل به همراه اطلاعات مالی ارسال نشده است",
            "advantage_columns.required" => "لیست مزایای پرسنل ارسال نشده است",
            "year.required" => "سال مؤثر انتخاب نشده است",
            "daily_wage.required" => "درج دستمزد روزانه الزامی می باشد",
            "prior_service.required" => "درج پایه سنوات الزامی می باشد",
            "working_days.required" => "درج روزهای کارکرد الزامی می باشد",
            "occupational_group.required" => "درج گروه شغلی الزامی می باشد",
            "count_of_children.required" => "درج تعداد فرزندان تحت تکلف الزامی می باشد",
            "effective_year.required" => "انتخاب سال مؤثر الزامی می باشد"
        ];
    }
}
