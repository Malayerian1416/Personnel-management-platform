<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class CustomGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->method() == "POST") {
            return [
                "name" => "required",
                "color" => "required|unique:custom_groups",
                "employees" => "required|json|min:10",
                "description" => "sometimes|nullable"
            ];
        }
        else{
            return [
                "name" => "required",
                "color" => ['required','unique:custom_groups,color,'.$this->route('id').",id"],
                "employees" => "sometimes|nullable",
                "description" => "sometimes|nullable"
            ];
        }
    }

    #[ArrayShape(["name.required" => "string", "color.required" => "string", "color.unique" => "string", "employees.min" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام گروه الزامی می باشد",
            "color.required" => "انتخاب مشخصه رنگ گروه الزامی می باشد",
            "color.unique" => "مشخصه رنگ انتخاب شده تکراری می باشد",
            "employees.min" => "انتخاب حداقل یک پرسنل به عنوان عضو گروه الزامی می باشد"
        ];
    }
}
