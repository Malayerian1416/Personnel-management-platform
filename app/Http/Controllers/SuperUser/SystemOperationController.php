<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\SystemConfigurationLog;
use App\Models\SystemOptimization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SystemOperationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            return view("superuser.system_operations",[
                "optimizations" => SystemConfigurationLog::byType("Optimization"),
                "cacheConfigs" => SystemConfigurationLog::byType("CacheConfigs"),
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
    public function optimize(): \Illuminate\Http\RedirectResponse
    {
        try {
            Artisan::call("optimize:clear");
            SystemOptimization::query()->create(["user_id" => Auth::id()]);
            return redirect()->back()->with(["result" => "success", "message" => "successful"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical_error" => $error->getMessage()]);
        }
    }
}
