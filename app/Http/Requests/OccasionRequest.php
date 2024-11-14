<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class OccasionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["title" => "string", "description" => "string", "image" => "string"])] public function rules(): array
    {
        if ($this->method() == "post") {
            return [
                "title" => "sometimes|nullable",
                "description" => "sometimes|nullable",
                "image" => "required|mimes:jpg,jpeg,png,tiff,bmp,svg|max:3072000"
            ];
        }
        else{
            return [
                "title" => "sometimes|nullable",
                "description" => "sometimes|nullable",
                "image" => "sometimes|nullable|mimes:jpg,jpeg,png,tiff,bmp,svg|max:3072000",
            ];
        }
    }

    #[ArrayShape(["image.required" => "string", "image.mimes" => "string", "image.max" => "string"])] public function messages(): array
    {
        return [
            "image.required" => "انتخاب تصویر الزامی می باشد",
            "image.mimes" => "فرمت تصویر انتخاب شده قابل قبول نمی باشد",
            "image.max" => "حجم تصویر انتخاب شده بیش از حد تعیین شده می باشد"
        ];
    }
}
