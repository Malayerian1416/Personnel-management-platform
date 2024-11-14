<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckUpRegistration
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::exists("register.documents"))
            return $next($request);
        else
            return redirect()->back()->withErrors(["logical_error" => "مرحله بارگذاری تصویر مدارک تکمیل نشده است"]);
    }
}
