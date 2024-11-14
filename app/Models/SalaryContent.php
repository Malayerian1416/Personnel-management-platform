<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryContent extends Model
{
    use HasFactory;
    protected $table = "salary_contents";
    protected $fillable = ["user_id","category_id","title","amount"];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SalaryContentCategory::class,"category_id");
    }
}
