<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use function Clue\StreamFilter\fun;
use function VeeWee\Xml\Xslt\Configurator\functions;

class Contract extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "contracts";
    protected $fillable = ["user_id","organization_id","parent_id","name","number","start_date","end_date","inactive","files","is_parent"];
    protected $appends = ["destroy_route"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class,"organization_id");
    }
    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class,"contract_id");
    }
    public function pre_employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ContractPreEmployee::class,"contract_id");
    }
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contract::class,"parent_id");
    }
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"parent_id");
    }
    public function permitted_staffs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class,"contract_user","contract_id","staff_id");
    }
    public function getDestroyRouteAttribute(): string
    {
        return route("Contracts.destroy",$this->id);
    }
    public function payslip_template(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PaySlipTemplate::class,"contract_id");
    }
    public static function GetPermitted(){
        if (User::UserType() == "staff")
            return array_column(self::query()->where("inactive",0)->where("user_id","=",Auth::id())->orWhereHas("permitted_staffs",function($query){
                $query->where("staff_id","=",Auth::id());
            })->get("id")->toArray(),"id");
        elseif (User::UserType() == "admin")
            return array_column(self::query()->where("inactive",0)->get("id")->toArray(),"id");
        else
            abort(403);
    }
}
