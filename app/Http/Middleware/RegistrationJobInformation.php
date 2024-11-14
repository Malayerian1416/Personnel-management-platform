<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegistrationJobInformation
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::exists("register.job_information"))
            return $next($request);
        else
            return redirect()->back()->withErrors(["logical_error" => "مرحله اطلاعات شغلی تکمیل نشده است"]);
    }
}
