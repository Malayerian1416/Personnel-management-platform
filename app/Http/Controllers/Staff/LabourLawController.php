<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabourLawRequest;
use App\Models\LabourLawTariff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Throwable;

class LabourLawController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"LabourLaw");
        try {
            return view("staff.labour_law", ["labour_laws" => LabourLawTariff::with("user")->get()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(LabourLawRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize("create","LabourLaw");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["daily_wage"] = Str::replace(",","",$validated["daily_wage"]);
            $validated["household_consumables_allowance"] = Str::replace(",","",$validated["household_consumables_allowance"]);
            $validated["housing_purchase_allowance"] = Str::replace(",","",$validated["housing_purchase_allowance"]);
            $validated["marital_allowance"] = Str::replace(",","",$validated["marital_allowance"]);
            $validated["child_allowance"] = Str::replace(",","",$validated["child_allowance"]);
            LabourLawTariff::query()->create($validated);
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
        Gate::authorize('edit',"LabourLaw");
        try {
            return view("staff.edit_labour_law", ["labour_law" => LabourLawTariff::query()->findOrFail($id)]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(LabourLawRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize("edit","LabourLaw");
        try {;
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["daily_wage"] = Str::replace(",","",$validated["daily_wage"]);
            $validated["household_consumables_allowance"] = Str::replace(",","",$validated["household_consumables_allowance"]);
            $validated["housing_purchase_allowance"] = Str::replace(",","",$validated["housing_purchase_allowance"]);
            $validated["marital_allowance"] = Str::replace(",","",$validated["marital_allowance"]);
            $validated["child_allowance"] = Str::replace(",","",$validated["child_allowance"]);
            LabourLawTariff::query()->findOrFail($id)->update($validated);
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
        Gate::authorize("delete","LabourLaw");
        try {
            DB::beginTransaction();
            LabourLawTariff::query()->findOrFail($id)->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
