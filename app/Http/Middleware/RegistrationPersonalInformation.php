<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegistrationPersonalInformation
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::exists("register.personal_information"))
            return $next($request);
        else
            return redirect()->back()->withErrors(["logical_error" => "مرحله اطلاعات شخصی تکمیل نشده است"]);
    }
}
