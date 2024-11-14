<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["name" => "string", "organization_id" => "string", "number" => "string", "is_parent" => "string", "start_date" => "string", "end_date" => "string", "employees" => "string", "children_subset_list" => "string", "upload_file.*" => "string"])] public function rules(): array
    {
        return [
            "name" => "required",
            "organization_id" => "required",
            "number" => "sometimes|nullable",
            "is_parent" => "sometimes|nullable",
            "start_date" => "required|jdate:Y/m/d",
            "end_date" => "required|jdate:Y/m/d|jdate_after:{$this->input('start_date')},Y/m/d",
            "employees" => "sometimes|nullable",
            "children_subset_list" => "required_with:is_parent",
            "upload_file.*" => "sometimes|nullable|mimes:png,jpg,bmp,tiff,pdf,xlsx,xls,txt,docx",
        ];
    }

    #[ArrayShape(["name.required" => "string", "organization_id.required" => "string", "start_date.required" => "string", "start_date.jdate" => "string", "end_date.required" => "string", "end_date.jdate" => "string", "end_date.jdate_after" => "string", "children_subset_list.required_with" => "string", "upload_files.*.mimes" => "string"])] public function messages(): array
    {
        return [
            "name.required" => "درج نام قرارداد الزامی می باشد",
            "organization_id.required" => "انتخاب سازمان الزامی می باشد",
            "start_date.required" => "انتخاب تاریخ شروع قرارداد الزامی می باشد",
            "start_date.jdate" => "فرمت تاریخ شروع قرارداد صحیح نمی باشد",
            "end_date.required" => "انتخاب تاریخ پایان قرارداد الزامی می باشد",
            "end_date.jdate" => "فرمت تاریخ پایان قرارداد صحیح نمی باشد",
            "end_date.jdate_after" => "فرمت تاریخ پایان قرارداد باید بعد از تاریخ شروع قرارداد انتخاب شود",
            "children_subset_list.required_with" => "ایجاد حداقل یک عنوان فرزند الزامی می باشد",
            "upload_files.*.mimes" => "فرمت فایل(های) مستندات قابل قبول نمی باشد"
        ];
    }
}
