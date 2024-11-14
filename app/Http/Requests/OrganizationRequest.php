<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class OrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
        ];
    }

    #[ArrayShape(["name.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام سازمان الزامی می باشد",
        ];
    }
}
