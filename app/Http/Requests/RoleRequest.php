<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required",
            "role_menu" => "required",
        ];
    }
    public function messages()
    {
        return [
            "name.required" => "درج نام برای عنوان شغلی الزامی می باشد.",
            "role_menu.required" => "انتخاب حداقل یک آیتم منو برای عنوان شغلی الزامی می باشد.",
        ];
    }
}
