<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\OccasionRequest;
use App\Models\Occasion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class OccasionController extends Controller
{

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"Occasions");
        try {
            $occasion = Occasion::query()->with("user")->orderBy("updated_at","desc")->get();
            return view("staff.occasions",["occasions" => $occasion]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(OccasionRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Occasions");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["image"] = $request->file("image")->hashName();
            $news = Occasion::query()->create($validated);
            $image = $this->image_resize($request->file("image"));
            Storage::disk("occasions")->put("{$news->id}/{$request->file('image')->hashName()}",$image);
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
        Gate::authorize('edit',"Occasions");
        try {
            $occasion = Occasion::query()->findOrFail($id);
            return view("staff.edit_occasion",["occasion" => $occasion]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(OccasionRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"Occasions");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $occasion = Occasion::query()->findOrFail($id);
            if ($request->hasFile("image")){
                $image = $this->image_resize($request->file("image"));
                Storage::disk('occasions')->put("$occasion->id/{$request->file('image')->hashName()}", $image);
                Storage::disk("occasions")->delete("$id/$occasion->image");
                $occasion->update(["image" => $request->file("image")->hashName()]);
            }
            $occasion->update([
                "user_id" => Auth::id(),
                "title" => $validated["title"],
                "description" => $validated["description"],
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
        Gate::authorize('delete',"Occasions");
        try {
            DB::beginTransaction();
            Occasion::query()->findOrFail($id)->delete();
            Storage::disk("occasions")->deleteDirectory($id);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $occasion = Occasion::query()->findOrFail($id);
        if ($occasion->publish == 1)
            $occasion->update(["publish" => 0]);
        else
            $occasion->update(["publish" => 1]);
        $result = match($occasion->publish){
            1 => "active",
            0 => "inactive",
            default => "unknown"
        };
        return redirect()->back()->with(["result" => "success","message" => $result]);
    }
}
