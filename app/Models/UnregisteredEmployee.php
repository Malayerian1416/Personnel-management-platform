<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnregisteredEmployee extends Model
{
    use HasFactory;
    protected $table = "unregistered_employees";
    protected $fillable = ["name","national_code","organization_id","mobile","description"];

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class,"organization_id");
    }
}
