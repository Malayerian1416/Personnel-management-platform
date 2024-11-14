<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractSubsetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => "required",
            "contract_id" => "required",
            "parent_id" => "sometimes|nullable",
            "workplace" => "sometimes|nullable",
            "excel_file" => "sometimes|nullable|mimes:xlsx",
            "upload_file.*" => "sometimes|nullable|mimes:png,jpg,bmp,tiff,pdf,xlsx,xls,txt",
        ];
    }
    public function messages()
    {
        return [
            "name.required" => "درج نام قرارداد الزامی می باشد",
            "contract_id.required" => "انتخاب قرارداد الزامی می باشد",
            "excel_file.mimes" => "فرمت فایل اکسل مشخصات پرسنل قابل قبول نمی باشد",
            "upload_files.*.mimes" => "فرمت فایل(های) مستندات قابل قبول نمی باشد"
        ];
    }
}
