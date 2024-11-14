<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaySlipTemplateRequest;
use App\Models\PaySlipTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Throwable;

class PaySlipTemplateController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"PaySlipTemplates");
        try {
            return view("staff.payslip_templates", [
                "templates" => PaySlipTemplate::GetPermitted(),
                "organizations" => $this->allowed_contracts("tree")
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(PaySlipTemplateRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"PaySlipTemplates");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            foreach ($validated["contract_id"] as $contract_id){
                PaySlipTemplate::query()->updateOrCreate(["contract_id" => $contract_id],[
                    "user_id" => Auth::id(),
                    "columns" => $validated["excel_columns"],
                    "national_code_index" => $validated["national_code_index"]
                ]);
            }
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
        Gate::authorize('edit',"PaySlipTemplates");
        try {
            return view("staff.edit_payslip_template", [
                "template" => PaySlipTemplate::query()->with("contract")->findOrFail($id),
                "organizations" => $this->allowed_contracts("tree")
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(PaySlipTemplateRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"PaySlipTemplates");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            PaySlipTemplate::query()->updateOrCreate(["contract_id" => $validated["contract_id"]],[
                "user_id" => Auth::id(),
                "columns" => $validated["excel_columns"],
                "national_code_index" => $validated["national_code_index"]
            ]);
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
        Gate::authorize('delete',"PaySlipTemplates");
        try {
            DB::beginTransaction();
            $payslip = PaySlipTemplate::query()->findOrFail($id);
            $payslip->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
