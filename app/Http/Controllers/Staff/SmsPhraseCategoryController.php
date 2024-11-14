<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\SmsPhraseCategoryRequest;
use App\Models\SmsPhraseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class SmsPhraseCategoryController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"SmsPhraseCategory");
        try {
            return view("staff.sms_phrase_category",["categories" => SmsPhraseCategory::query()->with("user")->get()]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(SmsPhraseCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"SmsPhraseCategory");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            SmsPhraseCategory::query()->create($validated);
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"SmsPhraseCategory");
        try {
            $category = SmsPhraseCategory::query()->findOrFail($id);
            return view("staff.edit_sms_phrase_category",["category" => $category]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(SmsPhraseCategoryRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"SmsPhraseCategory");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $category = SmsPhraseCategory::query()->findOrFail($id);
            $category->update($validated);
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"SmsPhraseCategory");
        try {
            $category = SmsPhraseCategory::query()->with("phrases")->findOrFail($id);
            if ($category->phrases->isNotEmpty())
                return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            $category->delete();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $category = SmsPhraseCategory::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($category)]);
    }
}
