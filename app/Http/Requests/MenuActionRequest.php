<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuActionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required",
            "action" => "required",
        ];
    }
    public function messages()
    {
        return [
            "name.required" => "وارد کردن نام الزامی می باشد.",
            "action.required" => "وارد کردن عملیات الزامی می باشد."
        ];
    }
}
