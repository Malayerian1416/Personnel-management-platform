<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomGroup extends Model
{
    use HasFactory;
    protected $table = "custom_groups";
    protected $fillable = ["user_id","name","color","description","inactive"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function employees(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CustomGroupEmployee::class,"group_id");
    }
}
