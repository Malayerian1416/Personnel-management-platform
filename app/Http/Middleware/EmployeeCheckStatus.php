<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeCheckStatus
{
    public function handle(Request $request, Closure $next)
    {
        $employee = Employee::query()->with("data_refresh")->findOrFail(Auth::user()->employee_id);
        $data = $employee->data_refresh;
        if ($data){
            if ($data->is_loaded == 0)
                return redirect()->route("EmployeeRefreshData.index");
            else
                return redirect()->route("EmployeeRefreshData.waiting");
        }
        else
            return $next($request);
    }
}
