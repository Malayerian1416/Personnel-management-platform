<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class OrganizationController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Organizations");
        try {
            return view("staff.organizations",["organizations" => Organization::with("user")->get()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function store(OrganizationRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Organizations");
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $validated["user_id"] = Auth::id();
            Organization::query()->create($validated);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Organizations");
        try {
            return view("staff.edit_organization",["organization" => Organization::query()->findOrFail($id)]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }

    }
    public function update(OrganizationRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Organizations");
        try {
            $validated = $request->validated();
            DB::beginTransaction();
            $validated["user_id"] = Auth::id();
            Organization::query()->findOrFail($id)->update($validated);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"Organizations");
        try {
            $organization = Organization::query()->findOrFail($id);
            if ($organization->contracts()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            else{
                DB::beginTransaction();
                $organization->delete();
                DB::commit();
                return redirect()->back()->with(["result" => "success","message" => "deleted"]);
            }
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
    public function status($id): \Illuminate\Http\RedirectResponse
    {
        return redirect()->back()->with(["result" => "success","message" => $this->activation(Organization::query()->findOrFail($id))]);
    }
}
