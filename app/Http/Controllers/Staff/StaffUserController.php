<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffUserRequest;
use App\Models\Contract;
use App\Models\ContractSubset;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;
use function Clue\StreamFilter\fun;

class StaffUserController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('index',"StaffUsers");
        try {
            $users = (User::UserType() == "staff" ? User::query()->with(["user","role"])->where("id","<>",Auth::id())->where("is_staff","=",1)->get() : User::UserType() == "admin") ? User::query()->where("is_super_user","=",0)->where("is_staff","=",1)->with(["user","role"])->get() : abort(403);
            $roles = Role::all();
            return view("staff.users",["users" => $users,"roles" => $roles,"organizations" => $this->allowed_contracts("tree")]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function store(StaffUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create',"StaffUsers");
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            $validated["password"] = Hash::make($validated["password"]);
            $validated["is_staff"] = 1;
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

    public function edit($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        Gate::authorize('edit',"StaffUsers");
        try {
            $sign = '';$avatar = '';
            $staff_user = User::query()->with(["contracts" => function($query){
                $query->where("contracts.inactive","=",0);
            }])->findOrFail($id);
            $roles = Role::all();
            if ($staff_user->sign && Storage::disk("staff_signs")->exists("$staff_user->id/$staff_user->sign"))
                $sign = base64_encode(Storage::disk("staff_signs")->get("$staff_user->id/$staff_user->sign"));
            if ($staff_user->avatar && Storage::disk("avatars")->exists("$staff_user->id/$staff_user->avatar"))
                $avatar = base64_encode(Storage::disk("avatars")->get("$staff_user->id/$staff_user->avatar"));
            return view("staff.edit_user",[
                "staff_user" => $staff_user,
                "roles" => $roles,
                "contracts" => $this->allowed_contracts("tree"),
                "sign" => $sign,
                "avatar" => $avatar
            ]);
        }
        catch (Throwable $error){
            return redirect()->back()->withErrors(["logical" => $error->getMessage()]);
        }
    }

    public function update(StaffUserRequest $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize("edit","StaffUsers");
        try {
            $validated = $request->validated();
            $validated["user_id"] = Auth::id();
            if ($validated["password"] != null && $validated["password"] != "")
                $validated["password"] = Hash::make($validated["password"]);
            else
                unset($validated["password"]);
            $user = User::query()->findOrFail($id);
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
        Gate::authorize("delete","StaffUsers");
        try {
            $user = User::query()->with(["role","menu_headers","menu_items","menu_actions"])->findOrFail($id);
            $user->contracts()->detach();
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
