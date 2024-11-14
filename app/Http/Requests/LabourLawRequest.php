<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabourLawRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => "required",
            "daily_wage" => "required",
            "household_consumables_allowance" => "required",
            "housing_purchase_allowance" => "required",
            "marital_allowance" => "required",
            "child_allowance" => "required",
            "effective_year" => "required"
        ];
    }
    public function messages()
    {
        return [
            "name.required" => "درج عنوان الزامی است",
            "daily_wage.required" => "درج دستمزد روزانه الزامی است",
            "household_consumables_allowance.required" => "درج کمک هزینه خرید اقلام مصرفی خانوار الزامی است",
            "housing_purchase_allowance.required" => "درج کمک هزینه خرید مسکن الزامی است",
            "marital_allowance.required" => "درج حق تاهل الزامی است",
            "child_allowance.required" => "درج حق اولاد الزامی است",
            "effective_year.required" => "انتخاب سال مؤثر الزامی است"
        ];
    }
}
