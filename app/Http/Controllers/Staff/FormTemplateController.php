<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ApplicationForm;
use App\Models\BackgroundCheckApplication;
use App\Models\EmploymentCertificateApplication;
use App\Models\FormTemplate;
use App\Models\LoanPaymentConfirmationApplication;
use App\Models\OccupationalMedicineApplication;
use App\Models\SettlementFormApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class FormTemplateController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"FormTemplates");
        try {
            $applications = ApplicationForm::all();
            $form_templates = FormTemplate::query()->with(["application","user"])->get();
            return view("staff.form_templates", ["applications" => $applications,"form_templates" => $form_templates]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"FormTemplates");
        try {
            DB::beginTransaction();
            $request->validate(["page_contents" => "required"],["page_contents.required" => "اطلاعات به طور صحیح ارسال نشده است"]);
            $page_contents = json_decode($request->input("page_contents"),true);
            if($page_contents){
                $application_form = ApplicationForm::query()->updateOrCreate(["application_form_type" => $page_contents["application"]],["name" => $page_contents["application"]]);
                $form_template = FormTemplate::query()->create([
                    "user_id" => Auth::id(),
                    "application_form_id" => $application_form->id,
                    "name" => $page_contents["name"],
                    "page_data" =>	json_encode($page_contents,JSON_UNESCAPED_UNICODE)
                ]);
                if ($page_contents["background"] && $request->hasFile("background")) {
                    Storage::disk('form_templates')->put($form_template->id, $request->file("background"));
                    $form_template->update(["background" => $request->file("background")->hashName()]);
                }
                DB::commit();
                return redirect()->back()->with(["result" => "success","message" => "saved"]);
            }
            else
                throw ValidationException::withMessages(['page' => 'اطلاعات فرم قالب دارای فرمت صحیحی نمی باشد']);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"FormTemplates");
        try {
            $background = "";
            $applications = [
                ["name" => "گواهی اشتغال به کار", "model" => "EmploymentCertificateApplication"],
                ["name" => "نامه کسر از حقوق(به همراه ضمانت)", "model" => "LoanPaymentConfirmationApplication"],
                ["name" => "طب کار", "model" => "OccupationalMedicineApplication"],
                ["name" => "سوءپیشینه", "model" => "BackgroundCheckApplication"],
                ["name" => "فرم تسویه حساب", "model" => "SettlementFormApplication"]
            ];
            $form_template = FormTemplate::query()->with("application")->findOrFail($id);
            if ($form_template->background && Storage::disk("form_templates")->exists("{$form_template->id}/$form_template->background"))
                $background = base64_encode(Storage::disk("form_templates")->get("{$form_template->id}/$form_template->background"));
            return view("staff.edit_form_template", [
                "form_template" => $form_template,
                "background" => $background,
                "applications" => $applications
            ]);

        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"FormTemplates");
        try {
            DB::beginTransaction();
            $request->validate(["page_contents" => "required"],["page_contents.required" => "اطلاعات به طور صحیح ارسال نشده است"]);
            $page_contents = json_decode($request->input("page_contents"),true);
            if($page_contents){
                $form_template = FormTemplate::query()->findOrFail($id);
                $form_template->update([
                    "user_id" => Auth::id(),
                    "name" => $page_contents["name"],
                    "page_data" =>	json_encode($page_contents,JSON_UNESCAPED_UNICODE)
                ]);
                if ($page_contents["background"] && $request->hasFile("background")) {
                    Storage::disk('form_templates')->delete($form_template->id, $form_template->background);
                    Storage::disk('form_templates')->put($form_template->id, $request->file("background"));
                    $form_template->update(["background" => $request->file("background")->hashName()]);
                }
                DB::commit();
                return redirect()->back()->with(["result" => "success","message" => "updated"]);
            }
            else
                throw ValidationException::withMessages(['page' => 'اطلاعات فرم قالب دارای فرمت صحیحی نمی باشد']);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"FormTemplates");
        try {
            DB::beginTransaction();
            $form_template = FormTemplate::query()->findOrFail($id);
            Storage::disk('form_templates')->deleteDirectory($form_template->id);
            $form_template->delete();
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }
}
