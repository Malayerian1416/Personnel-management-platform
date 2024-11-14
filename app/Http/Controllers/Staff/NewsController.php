<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class NewsController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"News");
        try {
            $news = News::query()->with("user")->orderBy("id","desc")->get();
            return view("staff.news",["news" => $news]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(NewsRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"News");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["image"] = $request->file("main_image")->hashName();
            $news = News::query()->create($validated);
            $image = $this->image_resize($request->file("main_image"));
            Storage::disk("news")->put("{$news->id}/{$request->file('main_image')->hashName()}",$image);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $image = $this->image_resize($file);
                    Storage::disk('news')->put("$news->id/{$file->hashName()}", $image);
                }
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
        Gate::authorize('edit',"News");
        try {
            $news = News::query()->findOrFail($id);
            $images = Storage::disk("news")->allFiles($news->id);
            return view("staff.edit_news",["news" => $news, "images" => $images]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(NewsRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"News");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $news = News::query()->findOrFail($id);
            if ($request->hasFile("main_image")){
                $image = $this->image_resize($request->file("main_image"));
                Storage::disk('news')->put("$news->id/{$request->file('main_image')->hashName()}", $image);
                Storage::disk("news")->delete("$id/$news->image");
                $news->update(["image" => $request->file("main_image")->hashName()]);
            }
            $news->update([
                "user_id" => Auth::id(),
                "title" => $validated["title"],
                "topic" => $validated["topic"],
                "brief" => $validated["brief"],
                "description" => $validated["description"],
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file){
                    $image = $this->image_resize($file);
                    Storage::disk('news')->put("$news->id/{$file->hashName()}", $image);
                }
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
        Gate::authorize('delete',"News");
        try {
            DB::beginTransaction();
            News::query()->findOrFail($id)->delete();
            Storage::disk("news")->deleteDirectory($id);
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function delete_image($id,$file): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('delete',"News");
        try {
            Storage::disk("news")->delete("$id/$file");
            return redirect()->back()->with(["result" => "success","message" => "deleted"]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function status($id): \Illuminate\Http\RedirectResponse
    {
        $news = News::query()->findOrFail($id);
        if ($news->published == 1)
            $news->update(["published" => 0]);
        else
            $news->update(["published" => 1]);
        $result = match($news->published){
            1 => "active",
            0 => "inactive",
            default => "unknown"
        };
        return redirect()->back()->with(["result" => "success","message" => $result]);
    }
}
