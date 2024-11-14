<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StepFiveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    #[ArrayShape(["job_seating" => "string", "job_title" => "string", "bank_name" => "string", "bank_account" => "string", "credit_card" => "string", "sheba_number" => "string", "insurance_number" => "string", "insurance_days" => "string"])] public function rules(): array
    {
        return [
            "job_seating" => "required",
            "job_title" => "required",
            "bank_name" => "required",
            "bank_account" => "required",
            "credit_card" => "sometimes|nullable|min:19",
            "sheba_number" => "required|string|min:29",
            "insurance_number" => "sometimes|nullable",
            "insurance_days" => "sometimes|nullable"
        ];
    }

   #[ArrayShape(["job_seating.required" => "string", "job_title.required" => "string", "bank_name.required" => "string", "bank_account.required" => "string", "credit_card.required" => "string", "credit_card.min" => "string", "sheba_number.required" => "string", "sheba_number.min" => "string"])] public function messages(): array
    {
        return [
            "job_seating.required" => "درج محل استقرار الزامی می باشد",
            "job_title.required" => "درج عنوان شغلی الزامی می باشد",
            "bank_name.required" => "انتخاب نام بانک الزامی می باشد",
            "bank_account.required" => "درج شماره حساب بانکی الزامی می باشد",
            "credit_card.min" => "شماره کارت وارد شده ناقص می باشد",
            "sheba_number.required" => "درج شماره شبا حساب الزامی می باشد",
            "sheba_number.min" => "شماره شبا وارد شده ناقص می باشد",
        ];
    }
}
