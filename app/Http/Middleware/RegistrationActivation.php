<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegistrationActivation
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::exists("register.activation"))
            return $next($request);
        else
            return redirect()->route("step_one")->withErrors(["no_activation" => "فعال سازی مراحل ثبت نام شما فعال نگردیده است"]);
    }
}
