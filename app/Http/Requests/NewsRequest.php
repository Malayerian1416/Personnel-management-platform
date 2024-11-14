<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class NewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["title" => "string", "topic" => "string", "brief" => "string", "description" => "string", "main_image" => "string", "images" => "string", "images.*" => "string"])] public function rules(): array
    {
        if ($this->method() == "post") {
            return [
                "title" => "required",
                "topic" => "sometimes|nullable",
                "brief" => "required",
                "description" => "sometimes|nullable",
                "main_image" => "required|mimes:jpg,jpeg,png,tiff,bmp,svg|max:3072000",
                "images" => "sometimes|nullable",
                "images.*" => "mimes:jpg,jpeg,png,tiff,bmp,svg|max:3072000"
            ];
        }
        else{
            return [
                "title" => "required",
                "topic" => "sometimes|nullable",
                "brief" => "required",
                "description" => "sometimes|nullable",
                "main_image" => "sometimes|nullable|mimes:jpg,jpeg,png,tiff,bmp,svg|max:3072000",
                "images" => "sometimes|nullable",
                "images.*" => "mimes:jpg,jpeg,png,tiff,bmp,svg|max:3072000"
            ];
        }
    }

    #[ArrayShape(["title.required" => "string", "brief.required" => "string", "main_image.required" => "string", "main_image.mimes" => "string", "main_image.max" => "string", "images.*.mimes" => "string", "images.*.max" => "string"])] public function messages(): array
    {
        return [
            "title.required" => "درج عنوان خبر الزامی می باشد",
            "brief.required" => "درج شرح مختصر الزامی می باشد",
            "main_image.required" => "انتخاب تصویر اصلی الزامی می باشد",
            "main_image.mimes" => "فرمت تصویر اصلی قابل قبول نمی باشد",
            "main_image.max" => "حجم فایل انتخاب شده بیش از حد تعیین شده می باشد",
            "images.*.mimes" => "فرمت تصویر(تصاویر) انتخاب شده قابل قبول نمی باشد",
            "images.*.max" => "حجم تصویر(تصاویر) انتخاب شده بیش از حد تعیین شده می باشد"
        ];
    }
}
