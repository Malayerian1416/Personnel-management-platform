<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryContentCategory extends Model
{
    use HasFactory;
    protected $table = "salary_content_categories";
    protected $fillable = ["user_id","name","effective_year","inactive"];

    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SalaryContent::class,"category_id");
    }
}
