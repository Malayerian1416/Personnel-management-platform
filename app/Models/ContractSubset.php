<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractSubset extends Model
{
    use HasFactory;use softDeletes;
    protected $table = "contract_subsets";
    protected $fillable = ["name","contract_id","parent_id","user_id","workplace","inactive","files"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function contract(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contract::class,"contract_id");
    }
    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Employee::class,"contract_subset_id");
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
        return $this->belongsToMany(User::class,"contract_subset_user","contract_subset_id","staff_id");
    }
}
