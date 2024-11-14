<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\AutomationFlowRequest;
use App\Models\AutomationFlow;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutomationFlowController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $automation_flows = AutomationFlow::query()->with(["user","details"])->get();
            $roles = Role::all();
            return view("superuser.automation_flow",["automation_flows" => $automation_flows, "roles" => $roles]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(AutomationFlowRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $automation_flow = AutomationFlow::query()->create($validated);
            $flow_roles = json_decode($validated["roles_list"],true);
            foreach ($flow_roles as $role){
                $automation_flow->details()->insert([
                    "automation_flow_id" => $automation_flow->id,
                    "role_id" => $role["id"],
                    "priority" => $role["same"] > 0 ? $role["same"] : $role["priority"],
                    "is_main_role" => $role["main_role"] ? 1 : 0,
                ]);
            }
            DB::commit();
            return redirect()->back()->with(["result" =>  "success" , "message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $roles = Role::all();
            $automation_flow = AutomationFlow::query()->with(["user","details"])->findOrFail($id);
            return view("superuser.edit_flow_automation",["automation_flow" => $automation_flow,"roles" => $roles,"flow_list" => AutomationFlow::make_flow_list($id)]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(AutomationFlowRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $automation_flow = AutomationFlow::query()->findOrFail($id);
            $automation_flow->update(["name" => $validated["name"]]);
            $flow_roles = json_decode($validated["roles_list"],true);
            if ($flow_roles) {
                $automation_flow->details()->delete();
                foreach ($flow_roles as $role) {
                    $automation_flow->details()->insert([
                        "automation_flow_id" => $automation_flow->id,
                        "role_id" => $role["id"],
                        "priority" => $role["same"] > 0 ? $role["same"] : $role["priority"],
                        "is_main_role" => $role["main_role"] ? 1 : 0,
                    ]);
                }
            }
            DB::commit();
            return redirect()->back()->with(["result" =>  "success" , "message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        try {
            DB::beginTransaction();
            $automation_flow = AutomationFlow::query()->findOrFail($id);
            $automation_flow->delete();
            DB::commit();
            return redirect()->back()->with(["result" =>  "success" , "message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $automation_flow = AutomationFlow::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($automation_flow)]);
    }
}
