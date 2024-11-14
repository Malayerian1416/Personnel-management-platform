<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class EmployeeRequestRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "application_form_type" => "string", "flow_id" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "application_form_type" => "required",
            "flow_id" => "required"
        ];
    }

    #[ArrayShape(["name.required" => "string", "application_form_type.required" => "string", "flow_id.required" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام درخواست الزامی می باشد",
            "application_form_type.required" => "درج کلاس درخواست الزامی می باشد",
            "flow_id.required" => "انتخاب گردش اتوماسیون الزامی می باشد",
        ];
    }
}
