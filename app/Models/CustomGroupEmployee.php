<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomGroupEmployee extends Model
{
    protected $table = "custom_group_employees";
    protected $fillable = ["group_id","employee_id"];
    public $timestamps = false;

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CustomGroup::class,"group_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class,"employee_id");
    }
}
