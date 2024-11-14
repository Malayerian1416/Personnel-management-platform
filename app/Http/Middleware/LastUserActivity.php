<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LastUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = User::query()->findOrFail(Auth::id());
            if ($user->last_activity < Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s'))
                $user->update(["last_activity" => date("Y-m-d H:i:s"),"last_ip_address" => $request->ip()]);
        }
        return $next($request);
    }
}
