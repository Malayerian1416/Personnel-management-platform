<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required",
            "menu_header_id" => "required",
            "parent_id" => "sometimes|nullable",
            "menu_action_id" => "sometimes|nullable",
            "short_name" => "required",
            "route" => "sometimes|nullable",
            "main" => "sometimes|nullable",
            "priority" => "sometimes|nullable",
            "upload_file" => "sometimes|nullable|mimes:png"
        ];
    }
    public function messages()
    {
        return [
            "name.required" => "درج نام الزامی می باشد.",
            "short_name.required" => "درج نام مختصر الزامی می باشد.",
            "menu_header_id.required" => "انتخاب سرفصل منو الزامی می باشد.",
            "menu_action_id.required" => "انتخاب حداقل یک عنوان از عملیات وابسته الزامی می باشد.",
            "main.required" => "انتخاب عملیات اصلی الزامی می باشد.",
            "upload_file.mimes" => "فرمت فایل آپلود شده png نمی باشد."
        ];
    }
}
