<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbKeyword extends Model
{
    use HasFactory;
    protected $table = "db_keywords";
    protected $fillable = ["name","tag","data","resource","style"];
}
