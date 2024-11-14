<?php

namespace App\Providers;

use App\Models\Automation;
use App\Models\CompanyInformation;
use App\Models\MenuHeader;
use App\Models\News;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;
use function Clue\StreamFilter\fun;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind("path.public",function (){
            return base_path()."/public_html";
        });
    }

    public function boot()
    {
        Paginator::useBootstrap();
        View::composer(['superuser.superuser_dashboard','staff.staff_dashboard','staff.admin_dashboard'], function ($view){
            $company_information = CompanyInformation::query()->first();
            $user = User::query()->with("role")->findOrFail(Auth::id());
            $user->avatar && Storage::disk("avatars")->exists("$user->id/$user->avatar") ? $avatar = base64_encode(Storage::disk("avatars")->get("$user->id/$user->avatar")) : $avatar = '';
            switch (User::UserType()){
                case "superuser":{
                    $view->with([
                        "company_information" => $company_information,
                        "user" => $user,
                        "agent" => new Agent(),
                        "avatar" => $avatar
                    ]);
                    break;
                }
                case "admin":{
                    $menu_headers = MenuHeader::query()->with(["items" => function($query){
                        $query->orderBy("priority");
                    },"items.actions","items.children"])
                        ->Where("user_only","=",0)
                        ->orderBy("priority")->get();
                    $view->with([
                        "company_information" => $company_information,
                        "user" => $user,
                        "menu_headers" => $menu_headers,
                        "avatar" => $avatar,
                        "agent" => new Agent()
                    ]);
                    break;
                }
                case "staff":{
                    $role = Role::query()->with("menu_items.actions")->findOrFail(Auth::user()->role_id);
                    $menu_headers = MenuHeader::query()->with(["items" => function($query){
                        $query->orderBy("priority");
                    },"items.actions","items.children"])
                        ->where("staff_only","=",1)
                        ->orWhere("admin_only","=",0)
                        ->Where("user_only","=",0)
                        ->orderBy("priority")->get();
                    $view->with([
                        "company_information" => $company_information,
                        "user" => $user,
                        "menu_headers" => $menu_headers,
                        "role" => $role,
                        "avatar" => $avatar,
                        "agent" => new Agent()
                    ]);
                    break;
                }
            }
        });
        View::composer(['user.*'], function ($view){
            $view->with(["agent" => new Agent(),"company_information" => CompanyInformation::query()->first()]);
        });
        View::composer(['layouts.authentication','layouts.introduction','layouts.registration','auth.*'], function ($view){
            $view->with(["agent" => new Agent(),"company_information" => CompanyInformation::query()->first()]);
        });
        View::composer(['layouts.landing'], function ($view){
            $agent = new Agent();
            $agent->isMobile() || $agent->isTablet() ? $paginate = 1 : $paginate = 4;
            $view->with([
                "news" => News::query()->where("published",1)->orderBy("updated_at","desc")->paginate($paginate),
                "company" => CompanyInformation::query()->with("ceo")->first()
            ]);
        });
        View::composer(['user.user_dashboard'], function ($view){
            $tickets = Ticket::query()->where("employee_id","=",Auth::user()->employee->id)
                ->where("is_read","=",0)->where("sender","=","expert")->count();
            $requests = Automation::query()->whereHas("employee",function ($query){
                $query->where("id","=",Auth::user()->employee->id);
            })->whereHas("user",function ($query){$query->where("id","=",Auth::id());})->where("is_finished","=",1)->where("is_read","=",0)->count();
            $view->with([
                "tickets" => $tickets,
                "requests" => $requests,
                "company_information" => CompanyInformation::query()->first()
            ]);
        });
    }
}
