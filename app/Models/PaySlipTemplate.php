<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PaySlipTemplate extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "payslip_templates";
    protected $fillable = ["user_id","contract_id","columns","national_code_index"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"contract_id");
    }

    public static function GetPermitted(): \Illuminate\Database\Eloquent\Collection|array
    {
        $contracts = Contract::GetPermitted();
        return self::query()->with(["user","contract"])->where("user_id","=",Auth::id())->orWhereIn("contract_id",$contracts)->get();
    }
}
