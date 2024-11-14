<?php

namespace App\Http\Requests;

use App\Rules\NationalCodeChecker;
use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class FollowUpRegistration extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["national_code" => "array", "tracking_code" => "string", "captcha" => "string"])] public function rules(): array
    {
        return [
            "national_code" => ["required",new NationalCodeChecker()],
            "tracking_code" => "required|numeric",
            "captcha" => "required|captcha"
        ];
    }
    #[ArrayShape(["national_code.required" => "string", "tracking_code.required" => "string", "tracking_code.numeric" => "string", "captcha.required" => "string", "captcha.captcha" => "string"])] public function messages(): array
    {
        return [
            "national_code.required" => "لطفا کد ملی خود را وارد نمایید",
            "tracking_code.required" => "درج کد رهگیری الزامی می باشد",
            "tracking_code.numeric" => "فرمت کد رهگیری صحیح نمی باشد",
            "captcha.required" => "لطفا کد امنیتی را وارد نمایید",
            "captcha.captcha" => "کد امنیتی وارد شده معتبر نمی باشد",
        ];
    }
}
