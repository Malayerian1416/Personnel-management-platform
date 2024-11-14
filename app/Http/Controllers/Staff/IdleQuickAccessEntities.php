<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Models\Contract;
use App\Models\ContractPreEmployee;
use App\Models\Employee;
use App\Models\EmployeeDataRequest;
use App\Models\Ticket;
use App\Models\UnregisteredEmployee;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Throwable;

class IdleQuickAccessEntities extends Controller
{
    public function publish(Request $request)
    {
        try {
            $type = $request->input("type");
            $expired_employees = [];
            $allowed_contracts = Contract::GetPermitted();
            $refreshes = [
                "count" => EmployeeDataRequest::query()->with(["employee"])->whereHas("employee",function ($query) use ($allowed_contracts) {
                    $query->whereIn("employees.contract_id", $allowed_contracts);
                })->where("is_loaded", "=", 1)->count(),
                "records" => EmployeeDataRequest::query()->with(["user", "employee.contract.organization"])->whereHas("employee",function ($query) use ($allowed_contracts) {
                    $query->whereIn("employees.contract_id", $allowed_contracts);
                })->where("is_loaded", "=", 1)->orderBy("updated_at","desc")->take(5)->get()
            ];
            $expired_employees["records"] = [];
            $expired_employees["count"] = 0;//count($expired_employees["records"]);
            $request_chart = Automation::statistics("MonthCounter");
            $registration_chart = ContractPreEmployee::statistics();
            $visit_chart = Visit::statistics();
            switch ($type) {
                case "registration":
                {
                    return [
                        "count" => ContractPreEmployee::NewRegistrationPaginate($this->allowed_contracts()->pluck("contracts.*.id")->flatten()->unique())->count(),
                        "records" => ContractPreEmployee::NewRegistrationPaginate($this->allowed_contracts()->pluck("contracts.*.id")->flatten()->unique())->take(5)
                    ];
                }
                case "automations":
                {
                    return [
                        "count" => Automation::GetPermitted()->count(),
                        "records" => Automation::GetPermitted()->take(5)
                    ];
                }
                case "tickets":
                {
                    return [
                        "count" => Ticket::LatestTickets()->count(),
                        "records" => Ticket::LatestTickets()->take(5)->toArray()
                    ];
                }
                case "unregistered":
                {
                    return [
                        "count" => UnregisteredEmployee::query()->with("organization")->count(),
                        "records" => UnregisteredEmployee::query()->with("organization")->take(5)->get()->toArray()
                    ];
                }
                case "refreshes":
                {
                    return $refreshes;
                }
                case "expired":
                {
                    return $expired_employees;
                }
                case "all":
                {
                    return [
                        "registration" => [
                            "count" => ContractPreEmployee::NewRegistrationPaginate($this->allowed_contracts()->pluck("contracts.*.id")->flatten()->unique())->count(),
                            "records" => ContractPreEmployee::NewRegistrationPaginate($this->allowed_contracts()->pluck("contracts.*.id")->flatten()->unique())->take(5)
                        ],
                        "automations" => [
                            "count" => Automation::GetPermitted()->count(),
                            "records" => Automation::GetPermitted()->take(5)
                        ],
                        "tickets" => [
                            "count" => Ticket::LatestTickets()->count(),
                            "records" => Ticket::LatestTickets()->take(5)->toArray()
                        ],
                        "unregistered" => [
                            "count" => UnregisteredEmployee::query()->with("organization")->count(),
                            "records" => UnregisteredEmployee::query()->with("organization")->take(5)->get()->toArray()
                        ],
                        "refreshes" => $refreshes,
                        "expired" => $expired_employees,
                        "requestChart" => $request_chart,
                        "registrationChart" => $registration_chart,
                        "visitChart" => $visit_chart
                    ];
                }
                case "default":
                    return [];

            }
            return [];
        }
        catch (Throwable $error){
            return $error->getMessage();
        }
    }
}
