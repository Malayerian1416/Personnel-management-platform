<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class AutomationFlowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "roles_list" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "roles_list" => "required"
        ];
    }

    #[ArrayShape(["name.required" => "string", "roles_list.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام الزامی می باشد",
            "roles_list.required" => "انتخاب حداقل یک عنوان شغلی برای گردش الزامی می باشد"
        ];
    }
}
