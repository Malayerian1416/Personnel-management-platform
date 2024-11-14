<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StepFourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "first_name" => "required",
            "last_name" => "required",
            "gender" => "required",
            "id_number" => "required|numeric",
            "father_name" => "required",
            "birth_date" => "required|jdate:Y/m/d",
            "birth_city" => "required",
            "issue_city" => "required",
            "education" => "required",
            "marital_status" => "required",
            "children_count" => "required|numeric",
            "included_children_count" => "required|numeric",
            "military_status" => "required",
            "phone" => "sometimes|nullable",
            "address" => "required",
            "email" => "sometimes|nullable|email"
        ];
    }

    public function messages()
    {
        return [
            "first_name.required" => "درج نام الزامی می باشد",
            "last_name.required" => "درج نام خانوادگی الزامی می باشد",
            "gender.required" => "انتخاب جنسیت الزامی می باشد",
            "id_number.required" => "درج شماره شناسنامه الزامی می باشد",
            "id_number.numeric" => "فرمت شماره شناسنامه صحیح نمی باشد",
            "father_name.required" => "درج نام پدر الزامی می باشد",
            "birth_date.required" => "درج نام الزامی می باشد",
            "birth_date.jdate" => "فرمت تاریخ تولد صحیح نمی باشد",
            "birth_city.required" => "انتخاب شهر محل تولد الزامی می باشد",
            "issue_city.required" => "انتخاب شهر محل صدور شناسنامه الزامی می باشد",
            "education.required" => "انتخاب میزان تحصیلات الزامی می باشد",
            "marital_status.required" => "انتخاب وضعیت تاهل الزامی می باشد",
            "children_count.required" => "درج تعداد کل فرزندان الزامی می باشد",
            "children_count.numeric" => "فرمت تعداد کل فرزندان صحیح نمی باشد",
            "included_children_count.required" => "درج تعداد فرزندان مشمول حق اولاد الزامی می باشد",
            "included_children_count.numeric" => "فرمت تعداد فرزندان مشمول حق اولاد صحیح نمی باشد",
            "military_status.required" => "انتخاب وضعیت سربازی الزامی می باشد",
            "address.required" => "درج آدرس منزل الزامی می باشد",
            "email.email" => "فرمت ایمیل وارد شده صحیح نمی باشد",
        ];
    }
}
