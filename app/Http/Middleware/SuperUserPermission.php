<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class SuperUserPermission
{
    public function handle(Request $request, Closure $next)
    {
        switch (User::UserType()){
            case "superuser": return $next($request);
            default : abort(403);
        }
    }
}
