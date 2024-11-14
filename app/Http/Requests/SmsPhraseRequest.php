<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class SmsPhraseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "text" => "string", "category_id" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "text" => "required",
            "category_id" => "required|numeric"
        ];
    }

    #[ArrayShape(["name.required" => "string", "text.required" => "string", "category_id.required" => "string", "category_id.numeric" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج عنوان پیامک الزامی می باشد",
            "text.required" => "درج متن پیامک الزامی می باشد",
            "category_id.required" => "انتخاب دسته بندی الزامی می باشد",
            "category_id.numeric" => "فرمت دسته بندی انتخاب شده صحیح نمی باشد"
        ];
    }
}
