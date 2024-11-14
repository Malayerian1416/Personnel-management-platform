<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\SmsPhraseRequest;
use App\Models\SmsPhrase;
use App\Models\SmsPhraseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;

class SmsPhraseController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"SmsPhrases");
        try {
            $phrases = SmsPhrase::query()->with(["category","user"])->get();
            $categories = SmsPhraseCategory::all();
            return view("staff.sms_phrases",["phrases" => $phrases, "categories" => $categories]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(SmsPhraseRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"SmsPhrases");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            SmsPhrase::query()->create($validated);
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"SmsPhrases");
        try {
            $phrase = SmsPhrase::query()->with(["category"])->findOrFail($id);
            $categories = SmsPhraseCategory::all();
            return view("staff.edit_sms_phrase",["phrase" => $phrase, "categories" => $categories]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(SmsPhraseRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"SmsPhrases");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $phrase = SmsPhrase::query()->findOrFail($id);
            $phrase->update($validated);
            return redirect()->back()->with(["result" => "success","message" => "updated"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"SmsPhrases");
        try {
            $phrase = SmsPhrase::query()->findOrFail($id);
            $phrase->delete();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function test($id): \Illuminate\Http\RedirectResponse
    {
        $category = SmsPhraseCategory::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($category)]);
    }
}
