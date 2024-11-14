<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class StaffPermission
{
    public function handle(Request $request, Closure $next)
    {
        if (Route::currentRouteName() === 'docs.image_print')
            return $next($request);
        else {
            switch (User::UserType()) {
                case "staff" || "admin":
                    return $next($request);
                default :
                    abort(403);
            }
        }
    }
}
