<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Image extends Model
{
    use HasFactory;
    protected $table = "images";
    protected $fillable = ["image_file_name","image_indicator"];
    public function domestic_news(): MorphToMany
    {
        return $this->morphedByMany(DomesticNews::class,"imageable","imageables");
    }
}
