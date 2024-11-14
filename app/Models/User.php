<?php

namespace App\Models;

use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, softDeletes;
    protected $fillable = [
        'user_id',
        'employee_id',
        'name',
        'gender',
        'username',
        'password',
        'email',
        'mobile',
        'email_verified_at',
        'mobile_verified_at',
        'is_super_user',
        'is_admin',
        'is_staff',
        'is_user',
        'remember_token',
        'role_id',
        'sign',
        'sign_hash',
        'avatar',
        'inactive',
        'last_activity',
        'last_ip_address'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
    ];

    public function hasPermission($action,$model): bool
    {
        return match (self::UserType()){
            "admin" => true,
            default => in_array("{$model}.{$action}",$this->role->menu_items()->pluck("role_menu.route")->toArray())
        };
    }
    public static function UserType(): string
    {
        $type = User::query()->findOrFail(Auth::id())->only(["is_super_user","is_admin","is_staff","is_user"]);
        return match (implode("",$type)){
            "1000" => "superuser",
            "0100" => "admin",
            "0010" => "staff",
            "0001" => "user",
            default => "unknown"
        };
    }
    public function MessagingPermission($action,$model): bool
    {
        if ($this->is_admin == 1)
            return true;
        else
            return in_array("{$model}.{$action}", $this->role->menu_items()->pluck("role_menu.route")->toArray());
    }
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class,"role_id");
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function menu_actions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuAction::class,"user_id");
    }
    public function menu_headers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuHeader::class,"user_id");
    }
    public function menu_items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItem::class,"user_id");
    }
    public function contracts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Contract::class,"contract_user","staff_id","contract_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id","id");
    }
    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class,"expert_id");
    }
    public static function RecipientAutomation($role,$contract): \Illuminate\Database\Eloquent\Collection|array
    {
        return User::query()->whereHas("contracts",function ($query) use ($contract){
            $query->where("contracts.id","=",$contract);})->whereHas("role",function ($query) use ($role){
            $query->where("roles.id","=",$role);
        })->get();
    }
    public static function RecipientAutomationCreation($contract_id): \Illuminate\Database\Eloquent\Collection|array
    {
        $recipients = [];
        $users = User::query()->with(["contracts" => function ($query) use ($contract_id){
            $query->where("contracts.id","=",$contract_id);}])->orWhere("is_admin","=",1)->get();
        foreach ($users as $user)
            if ($user->MessagingPermission("index","EmployeeRequestsAutomation"))
                $recipients[] = $user;
        return $recipients;
    }
    public static function RecipientRegistration($contract_id): \Illuminate\Database\Eloquent\Collection|array
    {
        $recipients = [];
        $users = User::query()->with(["contracts" => function ($query) use ($contract_id){
            $query->where("contracts.id","=",$contract_id);}])->orWhere("is_admin","=",1)->get();
        foreach ($users as $user)
            if ($user->MessagingPermission("index","EmployeesRecruiting"))
                $recipients[] = $user;
        return $recipients;
    }
    public static function RecipientPreRegistration($organization): \Illuminate\Database\Eloquent\Collection|array
    {
        $recipients = [];
        $users = User::query()->whereHas("contracts",function ($query) use ($organization){
            $query->where("contracts.organization_id","=",$organization);})->orWhere("is_admin","=",1)->get();
        foreach ($users as $user)
            if($user->MessagingPermission("index","UnregisteredEmployees"))
                $recipients[] = $user;
        return $recipients;
    }
    public static function RecipientReloading($contract_id): \Illuminate\Database\Eloquent\Collection|array
    {
        $recipients = [];
        $users = User::query()->with(["contracts" => function ($query) use ($contract_id){
            $query->where("contracts.id","=",$contract_id);}])->orWhere("is_admin","=",1)->get();
        foreach ($users as $user)
            if ($user->MessagingPermission("index","RefreshDataEmployees"))
                $recipients[] = $user;
        return $recipients;
    }
    public static function RecipientTicketing($contract_id): \Illuminate\Database\Eloquent\Collection|array
    {
        $recipients = [];
        $users = User::query()->with(["contracts" => function ($query) use ($contract_id){
            $query->where("contracts.id","=",$contract_id);}])->orWhere("is_admin","=",1)->get();
        foreach ($users as $user)
            if ($user->MessagingPermission("index","RefreshDataEmployees"))
                $recipients[] = $user;
        return $recipients;
    }
    public function GetSign(): ?string
    {
        if ($this->sign && Storage::disk("staff_signs")->exists("$this->id/$this->sign"))
            return "data:image/png;base64,".base64_encode(Storage::disk("staff_signs")->get("$this->id/$this->sign"));
        else
            return null;
    }
}
