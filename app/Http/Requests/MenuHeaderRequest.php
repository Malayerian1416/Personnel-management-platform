<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuHeaderRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required",
            "short_name" => "sometimes|nullable",
            "slug" => "required",
            "upload_file" => "sometimes|nullable|mimes:png"
        ];
    }
    public function messages()
    {
        return [
            "name.required" => "وارد کردن نام الزامی می باشد.",
            "slug.required" => "وارد کردن مشخصه الزامی می باشد.",
            "upload_file.mimes" => "فرمت فایل آپلود شده png نمی باشد."
        ];
    }
}
