<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Exception;
use Throwable;

class StaffSettingController extends Controller
{
    public function UsernameChange(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate(["username" => "required|min:8"],[
                "username.required" => "درج نام کاربری الزامی می باشد",
                "username.min" => "نام کاربری حداقل باید 8 کاراکتر باشد"
            ]);
            Auth::user()->update(["username" => $request->input("username")]);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function PasswordChange(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "old_password" => "required",
                'password' => ['required', 'min:8', 'confirmed']
            ],[
                "old_password.required" => "درج گذرواژه فعلی الزامی می باشد",
                "password.required" => "درج گذرواژه جدید الزامی می باشد",
                "password.min" => "گذرواژه جدید حداقل باید 8 کاراکتر باشد",
                "password.confirmed" => "تکرار گذرواژه جدید صحیح نمی باشد",
            ]);
            if(Hash::check($request->input("old_password"),Auth::user()->getAuthPassword())){
                Auth::user()->update(["password" => Hash::make($request->input("password"))]);
                DB::commit();
                return redirect()->back()->with(["result" => "success","message" => "updated"]);
            }
            else
                throw new Exception("گذرواژه فعلی وارد شده صحیح نمی باشد");
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
