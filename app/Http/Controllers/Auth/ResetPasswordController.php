<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');
        if ($request->has("email")) {
            return view('auth.passwords.reset')->with(
                ['token' => $token, 'email' => $request->email]
            );
        }
        elseif($request->has("mobile"))
            abort(404);
        else
            abort(403);

    }
    protected $redirectTo = RouteServiceProvider::HOME;
}
