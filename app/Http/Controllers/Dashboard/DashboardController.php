<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ApplicationForm;
use App\Models\Automation;
use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Models\EmployeeDataRequest;
use App\Models\Ticket;
use App\Models\TicketRoom;
use App\Models\UnregisteredEmployee;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DashboardController extends Controller
{
    public function idle(){
        $type = User::UserType();
        switch ($type){
            case "superuser":{
                return redirect()->route("superuser_idle");
            }
            case ($type == "staff" || $type == "admin"): {
                return redirect()->route("staff_idle");
            }
            case "user": {
                return redirect()->route("user_idle");
            }
            default : abort(403);
        }
    }
    public function superuser(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("superuser.idle");
    }
    public function staff()
    {
        try {
            if(User::UserType() == "admin" || User::UserType() == "staff")
                return view("staff.idle");
            else
                abort(403);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical_error" => $error]);
        }
    }
    public function user(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $tickets = Ticket::query()->where("employee_id","=",Auth::user()->employee->id)
            ->where("is_read","=",0)->where("sender","=","expert")->count();
        $requests = Automation::query()->whereHas("employee",function ($query){
            $query->where("id","=",Auth::user()->employee->id);
        })->whereHas("user",function ($query){$query->where("id","=",Auth::id());})
            ->where("is_finished","=",1)->where("is_read","=",0)->count();
        return view("user.idle",["tickets" => $tickets, "requests" => $requests]);
    }
}
