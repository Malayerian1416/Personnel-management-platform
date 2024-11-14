<?php

namespace App\Http\Requests;

use App\Rules\NationalCodeChecker;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StepOneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["national_code" => "array", "captcha" => "string"])] public function rules(): array
    {
        return [
            "national_code" => ["required",new NationalCodeChecker()],
            "captcha" => "required|captcha"
        ];
    }
    #[ArrayShape(["national_code.required" => "string", "captcha.required" => "string", "captcha.captcha" => "string"])] public function messages(): array
    {
        return [
            "national_code.required" => "لطفا کد ملی خود را وارد نمایید",
            "captcha.required" => "لطفا کد امنیتی را وارد نمایید",
            "captcha.captcha" => "کد امنیتی وارد شده معتبر نمی باشد",
        ];
    }
}
