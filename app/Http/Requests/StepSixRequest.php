<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class StepSixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["birth_certificate" => "string", "birth_certificate.*" => "string", "national_card" => "string", "military_certificate" => "string", "education_certificate" => "string", "personal_photo" => "string", "insurance_confirmation" => "string", "male_gender" => "string", "insurance_confirmed" => "string"])] public function rules(): array
    {
        return [
            "birth_certificate" => "required",
            "birth_certificate.*" => "mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048",
            "national_card" => "required|mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048",
            "military_certificate" => "required_with:male_gender|mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048",
            "education_certificate" => "required|mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048",
            "personal_photo" => "required|mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048",
            "insurance_confirmation" => "required_with:insurance_confirmed|mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048",
            "male_gender" => "sometimes|nullable",
            "insurance_confirmed" => "sometimes|nullable"
        ];
    }

    public function messages(): array
    {
        return [
            "birth_certificate.required" => "بارگذاری فایل های تصویر صفحات شناسنامه الزامی می باشد",
            "birth_certificate.min" => "بارگذاری حداقل 3 فایل جداگانه از صفحات شناسنامه الزامی می باشد",
            "birth_certificate.*.mimes" => "یک یا چند فایل تصویر صفحات شناسنامه دارای فرمت صحیح نمی باشد",
            "birth_certificate.*.max" => "حداکثر حجم مجاز برای هر فایل، 2 مگابایت می باشد",
            "national_card.required" => "بارگذاری فایل تصویر کارت ملی الزامی می باشد",
            "national_card.mimes" => "فایل تصویر کارت ملی دارای فرمت صحیح نمی باشد",
            "national_card.max" => "حجم فایل بارگذاری شده بیش از 2 مگابایت می باشد",
            "military_certificate.required_with" => "در صورت انتخاب جنسیت مرد، بارگذاری فایل تصویر کارت پایان الزامی می باشد",
            "military_certificate.mimes" => "فایل تصویر کارت پایان خدمت دارای فرمت صحیح نمی باشد",
            "military_certificate.max" => "حجم فایل بارگذاری شده بیش از 2 مگابایت می باشد",
            "education_certificate.required" => "بارگذاری فایل تصویر آخرین مدرک تحصیلی الزامی می باشد",
            "education_certificate.mimes" => "فایل تصویر آخرین مدرک تحصیلی دارای فرمت صحیح نمی باشد",
            "education_certificate.max" => "حجم فایل بارگذاری شده بیش از 2 مگابایت می باشد",
            "personal_photo.required" => "بارگذاری فایل تصویر پرسنلی الزامی می باشد",
            "personal_photo.mimes" => "فایل تصویر پرسنلی دارای فرمت صحیح نمی باشد",
            "personal_photo.max" => "حجم فایل بارگذاری شده بیش از 2 مگابایت می باشد",
            "insurance_confirmation.required_with" => "در صورت درج سابقه بیمه، بارگذاری فایل تصویر تاییدیه بیمه الزامی می باشد",
            "insurance_confirmation.mimes" => "فایل تصویر تاییدیه بیمه دارای فرمت صحیح نمی باشد",
            "insurance_confirmation.max" => "حجم فایل بارگذاری شده بیش از 2 مگابایت می باشد",
        ];
    }
}
