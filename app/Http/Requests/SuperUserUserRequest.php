<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuperUserUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): bool|array
    {
        if ($this->method() == "POST") {
            return [
                'is_admin' => "sometimes|nullable",
                'name' => ['required'],
                'username' => ['required', 'string', 'min:8', 'unique:users'],
                'role_id' => ['required'],
                'gender' => ["required"],
                'contracts' => "sometimes|nullable",
                'password' => ['required', 'min:8', 'confirmed'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                "mobile" => ["required", "regex:/^09(1[0-9]|9[0-2]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/", "unique:users"],
                "avatar" => "sometimes|nullable|mimes:jpg,jpeg,png",
                "sign" => "sometimes|nullable|mimes:png"
            ];
        }
        elseif ($this->method() == "PUT"){
            return [
                'is_admin' => "sometimes|nullable",
                'name' => ['required'],
                'username' => ['required', 'string', 'min:8', 'unique:users,username,'.$this->route('SuperUserUser').",id"],
                'role_id' => ['required'],
                'contracts' => "sometimes|nullable",
                'gender' => ["required"],
                'password' => ['sometimes','nullable', 'min:8', 'confirmed'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$this->route('SuperUserUser').",id"],
                "mobile" => ["required", "regex:/^09(1[0-9]|9[0-2]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/", "unique:users,mobile,".$this->route('SuperUserUser').",id"],
                "avatar" => "sometimes|nullable|mimes:jpg,jpeg,png",
                "sign" => "sometimes|nullable|mimes:png"
            ];
        }
        else
            return false;
    }

    public function messages(): array
    {
        return [
            "name.required" => "درج نام کاربر الزامی می باشد",
            "username.required" => "درج نام کاربری الزامی می باشد",
            "username.min" => "نام کاربری باید حداقل 8 کاراکتر باشد",
            "username.unique" => "نام کاربری وارد شده قبلا ثبت شده است",
            "role_id" => "انتخاب عنوان شغلی الزامی می باشد",
            "password.required" => "درج کلمه عبور الزامی می باشد",
            "password.min" => "کلمه عبور باید حداقل 8 کاراکتر باشد",
            "password.confirmed" => "کلمه عبور و تکرار آن همخوانی ندارند",
            "email.required" => "درج پست الکترونیکی الزامی می باشد",
            "email.email" => "پست الکترونیکی وارد شده صحیح نمی باشد",
            "email.max" => "طول رشته پست الکترونیکی بیشتر از 255 کاراکتر است",
            "email.unique" => "پست الکترونیکی وارد شده قبلا ثبت شده است",
            "mobile.required" => "درج شماره موبایل الزامی می باشد",
            "mobile.regex" => "فرمت شماره موبایل ثبت شده صحیح نمی باشد",
            "mobile.unique" => "شماره موبایل وارد شده قبلا ثبت شده است",
            "avatar.mimes" => "فایل تصویر پرسنلی در فرمت صحیح نمی باشد",
            "sign.mimes" => "فایل اسکن امضا در فرمت png نمی باشد"
        ];
    }
}
