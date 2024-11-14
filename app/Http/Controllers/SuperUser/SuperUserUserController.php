<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperUserUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SuperUserUserController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $users = User::query()->with(["user","role"])->where("is_staff","=",1)->orWhere("is_admin","=",1)->get();
        $roles = Role::all();
        return view("superuser.users",["users" => $users,"roles" => $roles,"organizations" => $this->allowed_contracts("tree")]);
    }

    public function store(SuperUserUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        try{
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["password"] = Hash::make($validated["password"]);
            if (isset($validated["is_admin"])){
                $validated["is_staff"] = 0;
                $validated["is_user"] = 0;
                $validated["is_admin"] = 1;
                $validated["is_superuser"] = 0;
            }
            else{
                $validated["is_staff"] = 1;
                $validated["is_user"] = 0;
                $validated["is_admin"] = 0;
                $validated["is_superuser"] = 0;
            }
            $user = User::query()->create($validated);
            if (isset($validated["contracts"]))
                $user->contracts()->syncWithPivotValues($validated["contracts"], ['user_id' => Auth::id()]);
            if ($request->hasFile("sign")){
                Storage::disk("staff_signs")->put($user->id,$request->file("sign"));
                $user->update(["sign" => $request->file("sign")->hashName(),"sign_hash" => Hash::make($user->id.$user->username)]);
            }
            if ($request->hasFile("avatar")){
                Storage::disk("avatars")->put($user->id,$request->file("avatar"));
                $user->update(["avatar" => $request->file("sign")->hashName()]);
            }
            DB::commit();
            return redirect()->back()->with(["result" => "success","message" => "saved"]);
        }
        catch (Throwable $error){
            DB::rollBack();
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $user = User::query()->with(["contracts" => function($query){
                $query->where("contracts.inactive","=",0);
            }])->findOrFail($id);
            $roles = Role::all();
            $sign = '';$avatar = '';
            if ($user->sign && Storage::disk("staff_signs")->exists("$user->id/$user->sign"))
                $sign = base64_encode(Storage::disk("staff_signs")->get("$user->id/$user->sign"));
            if ($user->avatar && Storage::disk("avatars")->exists("$user->id/$user->avatar"))
                $avatar = base64_encode(Storage::disk("avatars")->get("$user->id/$user->avatar"));
            return view("superuser.edit_user",[
                "user" => $user,
                "sign" => $sign,
                "avatar" => $avatar,
                "contracts" => $this->allowed_contracts("tree"),
                "roles" => $roles
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(SuperUserUserRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        try{
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            if ($validated["password"] != null && $validated["password"] != "")
                $validated["password"] = Hash::make($validated["password"]);
            else
                unset($validated["password"]);
            $user = User::query()->findOrFail($id);
            if (isset($validated["is_admin"])){
                $validated["is_staff"] = 0;
                $validated["is_user"] = 0;
                $validated["is_admin"] = 1;
                $validated["is_superuser"] = 0;
            }
            else{
                $validated["is_staff"] = 1;
                $validated["is_user"] = 0;
                $validated["is_admin"] = 0;
                $validated["is_superuser"] = 0;
            }
            $user->update($validated);
            if (isset($validated["contracts"]))
                $user->contracts()->syncWithPivotValues($validated["contracts"], ['user_id' => Auth::id()]);
            if ($request->hasFile("sign")){
                Storage::disk("staff_signs")->deleteDirectory($user->id);
                Storage::disk("staff_signs")->put($user->id,$request->file("sign"));
                $user->update(["sign" => $request->file("sign")->hashName(),"sign_hash" => Hash::make($user->id.$user->username)]);
            }
            if ($request->hasFile("avatar")){
                Storage::disk("avatars")->deleteDirectory($user->id);
                Storage::disk("avatars")->put($user->id,$request->file("avatar"));
                $user->update(["avatar" => $request->file("avatar")->hashName()]);
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
        try {
            $user = User::query()->with(["role","menu_headers","menu_items","menu_actions"])->findOrFail($id);
            foreach ($user->relationsToArray() as $relation){
                if ($relation != [] && $relation != null)
                    return redirect()->back()->with(["result" => "warning","message" => "relation_exists"]);
            }
            Storage::disk("staff_signs")->deleteDirectory("$user->id");
            Storage::disk("avatars")->deleteDirectory("$user->id");
            $user->delete();
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
        $user = User::query()->findOrFail($id);
        return redirect()->back()->with(["result" => "success","message" => $this->activation($user)]);
    }
}
