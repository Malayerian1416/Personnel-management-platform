<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ContractSubsetEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;
use function PHPUnit\Framework\returnArgument;
use function Psl\Internal\type;

class UserAxiosController extends Controller
{
    public function resend_verification_code(): array
    {
        try {
            $employee = ContractSubsetEmployee::employee(Session::get("register.national_code"));
            $verify_code = 12345;//rand(13254,97896);
            $text = env("SMS_ACTIVATION_CODE_TEXT")."\n\r{$verify_code}";
            //$this->send_sms([$employee->mobile],$text) > 0
            if(Session::get("register.sms_attempts") < 4) {
                if (1){
                    Session::put("register.sms_attempts",Session::get("register.sms_attempts") + 1);
                    $employee->update(["verify" => Hash::make($verify_code), "verify_timestamp" => date("Y-m-d H:i:s")]);
                    $seconds = $this->verify_remain_seconds($employee->verify_timestamp,Session::get("register.sms_attempts") >= 3);
                    return ["status" => 1,"message" => "کد فعال سازی با موفقیت ارسال شد","seconds" => Session::get("register.sms_attempts")];
                }
                else
                    return ["status" => 0,'message' => 'در حال حاضر امکان ارسال پیامک وجود ندارد'];
            }
            else {
                $seconds = $this->verify_remain_seconds($employee->verify_timestamp,true);
                return ["status" => 2, 'message' => 'به دلیل تعدد درخواست، پس از 5 دقیقه مجددا درخواست نمایید',"seconds" => Session::get("register.sms_attempts")];
            }

        }
        catch (Throwable $error){
            return ["status" => 0,'message' => $error->getMessage()];
        }
    }
}
