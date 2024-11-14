<?php

namespace App\Http\Controllers\Staff;

use App\Exports\ContractEmployeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Http\Requests\ContractSubsetRequest;
use App\Imports\ContractEmployeesImport;
use App\Imports\ImportCustomGroupExcel;
use App\Imports\PreContractEmployeeImport;
use App\Models\Contract;
use App\Models\ContractSubset;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ContractSubsetController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Contracts");
        try {
            return view("staff.contracts", [
                "organizations" => Organization::all(),
                "contracts" => Contract::query()->with(["user", "contract", "children","parent", "employees"])->get()
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(ContractRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Contracts");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["start_date"] = $this->Gregorian($validated["start_date"]);$validated["end_date"] = $this->Gregorian($validated["end_date"]);
            DB::beginTransaction();
            $contract = ContractSubset::query()->create($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_docs')->put($contract->id, $file);
                $contract->update(["files" => 1]);
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
        Gate::authorize('edit',"Contracts");
        try {
            return view("staff.edit_contract",[
                "organizations" =>  Organization::all(),
                "contracts" => Contract::all(),
                "contract" => Contract::query()->with(["organization","pre_employees.user","parent"])->findOrFail($id)
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(ContractRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"Contracts");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["start_date"] = $this->Gregorian($validated["start_date"]);$validated["end_date"] = $this->Gregorian($validated["end_date"]);
            DB::beginTransaction();
            $contract = Contract::query()->findOrFail($id);
            $contract->update($validated);
            if ($request->hasFile('upload_files')) {
                foreach ($request->file('upload_files') as $file)
                    Storage::disk('contract_docs')->put($contract->id, $file);
                $contract->update(["files" => 1]);
            }
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
        Gate::authorize('delete',"Contracts");
        try {
            DB::beginTransaction();
            $contract = Contract::query()->findOrFail($id);
            if ($contract->pre_employees()->exists() || $contract->employees()->exists())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            Storage::disk("contract_docs")->deleteDirectory($id);
            $contract->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function excel_download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new ContractEmployeesExport, 'contract_pre_employees.xlsx');
    }

    public function excel_upload(Request $request): array
    {
        try {
            $request->validate(
                ["excel_file" => "required|mimes:xlsx,xls"],
                ["excel_file.required" => "فایلی باگذاری نشده است","excel_file.mimes" => "فرمت فایل باگذاری شده صحیح نمی باشد"]
            );
            $response = [];
            $import_errors = [];
            $import = new PreContractEmployeeImport;
            $import->import($request->file("excel_file"));
            if (count($import->getFails()) > 0){
                foreach ($import->getFails() as $fail)
                    $import_errors [] = $fail;
            }
            if (count($import->failures()->toArray()) > 0){
                foreach ($import->failures() as $failure){
                    foreach ($failure->errors() as $error)
                        $import_errors [] = ["row" => $failure->row(),"message" => $error,"value" => $failure->values()[0]];
                }
            }
            if (count($import_errors) > 0)
                $message = "عملیات بارگذاری فایل وضعیت با موفقیت انجام شد اما ثبت اطلاعات فایل به طور کامل انجام نشد";
            else
                $message = "عملیات بارگذاری فایل وضعیت با موفقیت انجام شد";
            $response["result"] = "success";
            $response["message"] = $message;
            $response["import_errors"] = $import_errors;
            $response["data"] = $import->getResult();
            return $response;
        }
        catch (Throwable $error){
            $response["result"] = "fail";
            $response["message"] = $error->getMessage();
            $response["data"] = [];
            return $response;
        }
    }

    public function status($id): \Illuminate\Http\RedirectResponse
    {
        return redirect()->back()->with(["result" => "success","message" => $this->activation(Contract::query()->findOrFail($id))]);
    }

    public function download_docs($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
    {
        $status = $this->download($id,"contract_docs","private");
        if ($status["success"]) {
            $zip_file = Storage::disk("contract_docs")->path("/zip/{$id}/docs.zip");
            $zip_file_name = "contract_docs_" . verta()->format("Y-m-d H-i-s") . ".zip";
            return Response::download($zip_file,$zip_file_name,[],'inline');
        }
        else
            return redirect()->back()->withErrors(["logical" => $status["message"]]);
    }
}
