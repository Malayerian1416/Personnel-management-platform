<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected string $redirectTo = RouteServiceProvider::DASHBOARD;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function username(): string
    {
        return 'username';
    }
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha'
        ],[
            $this->username().".required" => "درج نام کاربری الزامی می باشد",
            "password.required" => "درج کلمه عبور الزامی می باشد.",
            "captcha.required" => "درج کد امنیتی اجباری می باشد.",
            "captcha.captcha" => "کد امنیتی وارد شده صحیح نمی باشد."
        ]);
    }
    public function redirectPath(): string
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/Dashboard';
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            "login_failed" => ["اطلاعات وارد شده در سیستم موجود نمی باشد!"]
        ]);
    }
    protected function authenticated(Request $request, $user): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        if ($user->inactive == 1) {
            Auth::logout();
            return redirect()->back()->withErrors(["baned" => "حساب کاربری شما مسدود می باشد"]);
        }
        if ($user->is_user){
            $employee = Employee::query()->findOrFail($user->employee_id);
            if (!$employee){
                Auth::logout();
                return redirect()->back()->withErrors(["baned" => "حساب کاربری شما فاقد اطلاعات پرسنلی می باشد"]);
            }
            if ($employee->detached){
                Auth::logout();
                return redirect()->back()->withErrors(["baned" => "حساب کاربری شما منقضی گردیده است"]);
            }
        }
        return redirect()->route("idle");
    }
    public function logout(Request $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        return redirect('/login');
    }
}
