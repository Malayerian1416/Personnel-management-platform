<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegistrationExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()->getName();
        switch ($route){
            case "step_one":{
                Session::forget([
                    "register.steps",
                    "register.current_step",
                    "register.national_code",
                    "register.sms_attempts",
                    "register.mobile",
                    "register.activation",
                    "register.gender",
                    "register.personal_information",
                    "register.insurance",
                    "register.job_information",
                ]);
                break;
            }
            case "step_two":{
                Session::forget(["register.mobile", "register.activation","register.personal_information"]);
            }
        }
        if (Session::exists("register.national_code"))
            return $next($request);
        else
            return redirect()->back()->withErrors(["logical_error" => "زمان ثبت نام شما منقضی شده است. لطفا دوباره سعی نمایید"]);
    }
}
