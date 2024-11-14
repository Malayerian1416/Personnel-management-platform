<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class ApplicationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    #[ArrayShape(["application_form_type" => "string", "recipient" => "string", "borrower" => "string", "loan_amount" => "string"])] public function rules(): array
    {
        return [
            "application_form_type" => "required",
            "recipient" => "required_if:application_form_type,LoanPaymentConfirmationApplication,EmploymentCertificateApplication",
            "borrower" => "sometimes|nullable",
            "loan_amount" => "required_if:application_form_type,LoanPaymentConfirmationApplication"
        ];
    }

    #[ArrayShape(["application_form_type.required" => "string", "recipient.required_if" => "string", "loan_amount.required_if" => "string"])] public function messages(): array
    {
        return [
            "application_form_type.required" => "انتخاب نوع درخواست الزامی می باشد",
            "recipient.required_if" => "در صورت انتخاب نامه کسر از حقوق و یا گواهی اشتغال به کار، درج نهاد درخواست کننده الزامی می باشد",
            "loan_amount.required_if" => "در صورت انتخاب نامه کسر از حقوق، درج مبلغ وام الزامی می باشد"
        ];
    }
}
