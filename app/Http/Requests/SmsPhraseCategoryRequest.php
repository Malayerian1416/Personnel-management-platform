<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class SmsPhraseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string"])] public function rules(): array
    {
        return [
            "name" => "required"
        ];
    }

    #[ArrayShape(["name.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج عنوان دسته بندی الزامی می باشد"
        ];
    }
}
