<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDataRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class RefreshDataController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $employee = Employee::query()->with("data_refresh")->findOrFail(Auth::user()->employee_id);
            return view("user.data_entry",["reload_data" => $employee->data_refresh]);
        }
        catch (Throwable $error) {
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function store($id,Request $request): \Illuminate\Http\RedirectResponse
    {
        $reload_data = EmployeeDataRequest::query()->with("employee")->findOrFail($id);
        $rules = [];
        $messages = [];
        foreach ($reload_data->docs as $doc) {
            $rules[$doc["data"]] = "required";
            $rules["{$doc["data"]}.*"] = "mimes:jpg,jpeg,png,svg,gif,tiff,bmp|max:2048";
            $messages["{$doc['data']}.required"] = "مقدار این فیلد را وارد نمایید";
            $messages["{$doc['data']}.*.mimes"] = "یک یا چند فایل تصویر بارگذاری شده دارای فرمت صحیح نمی باشد";
            $messages["{$doc['data']}.*.max"] = "حداکثر حجم مجاز برای هر فایل، 2 مگابایت می باشد";
        }
        foreach ($reload_data->databases as $database) {
            $rules[$database["data"]] = "required";
            $messages["{$database['data']}.required"] = "مقدار این فیلد را وارد نمایید";
        }
        $request->validate($rules, $messages);
        try {
            if ($reload_data->is_loaded == 1)
                return redirect()->back()->withErrors(["loaded" => "true"]);
            $employee = $reload_data->employee;
            foreach ($reload_data->docs as $doc) {
                if($request->hasFile($doc["data"])) {
                    Storage::disk("employee_docs")->deleteDirectory("/{$employee->national_code}/{$doc["data"]}");
                    foreach ($request->file($doc["data"]) as $file) {
                        $filename = Str::random(8) . ".jpg";
                        $image = $this->image_resize($file);
                        Storage::disk("employee_docs")->put("/{$employee->national_code}/{$doc["data"]}/{$filename}", $image);
                    }
                }
            }
            foreach ($reload_data->databases as $database) {
                $input = $request->has($database["data"]) ? $request->input($database["data"]) : null;
                if ($input)
                    $employee->update([$database["data"] => $input]);
            }
            $reload_data->update(["is_loaded" => 1]);
            $reload_data->update(["reload_date" => date("Y/m/d H:i:s")]);
            DB::commit();
            $notifications = Employee::employee($employee->national_code)->ReloadMessaging();
            $this->SendNotification($notifications["message"]["users"],$notifications["message"]["data"]);
            return redirect()->route("EmployeeRefreshData.waiting");
        }
        catch (Throwable $error) {
            DB::rollBack();
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function waiting(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $employee = Employee::query()->with("data_refresh")->findOrFail(Auth::user()->employee_id);
            return view("user.refresh_data_waiting",["reload_date" => verta($employee->data_refresh->reload_date)->format("Y/m/d")]);
        }
        catch (Throwable $error) {
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
}
