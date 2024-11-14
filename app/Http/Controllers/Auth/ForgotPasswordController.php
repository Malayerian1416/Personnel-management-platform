<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetSms;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            "via" => "required|in:email,sms",
            "email" => "required_if:via,email|email",
            "mobile" => "required_if:via,mobile|numeric",
            "captcha" => "required|captcha"
        ],[
            "via.required" => "نوع شیوه بازیابی معتبر نمی باشد",
            "via.in" => "نوع مسیر بازیابی معتبر نمی باشد",
            "email.required_if" => "درج ایمیل الزامی می باشد",
            "email.email" => "ایمیل وارد شده معتبر نمی باشد",
            "mobile.numeric" => "شماره تلفن همراه وارد شده معتبر نمی باشد",
            "captcha.required" => "لطفا کد امنیتی را وارد نمایید",
            "captcha.captcha" => "کد امنیتی وارد شده معتبر نمی باشد",
        ]);
        $user = null;
        switch ($request->input("via")){
            case "email": {
                $response = $this->broker()->sendResetLink(
                    $this->credentials($request)
                );
                return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
            }
            case "sms": {
                $user = User::query()->where("mobile" , "=", $request->input("mobile"))->first();
                break;
            }
        }
        if ($user){
            if($request->input("via") == "sms"){
                PasswordResetSms::query()->updateOrCreate(["mobile" => $user->mobile],["token" => Hash::make($user->mobile.date("Y/m/d H:i:s").rand(1000,9999))]);
                $sms_result = $this->send_sms([$user->mobile],env("SMS_RESET_PASSWORD"));
                if ($sms_result){
                    $response["result"] = "success";
                    $response["message"] = "عملیات با موفقیت انجام شد";
                }
                else{
                    $response["result"] = "fail";
                    $response["message"] = "ارسال پیامک با خطا مواجه شد";
                }
            }
            else
                return redirect()->back()->withErrors(["not_found" => "عملیات نامشخص می باشد"]);
        }
        else
            return redirect()->back()->withErrors(["not_found" => "مشخصات وارد شده در سامانه وجود ندارد"]);
    }
    protected function credentials(Request $request)
    {
        switch ($request->input("via")){
            case "email": {
                return $request->only('email');
            }
            case "sms": {
                return $request->only('mobile');
            }
        }
    }
}
