<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DomesticNews extends Model
{
    protected $table = "domestic_news";
    protected $fillable = ["title","description","release_date","role","user_id","publish","short_desc","view"];
    public function image(): MorphToMany
    {
        return $this->morphToMany(Image::class,"imageable","imageables")->select("images.id","image_file_name","image_indicator","imageable_id","imageable_type","role");
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function setPublish(){
        if($this->publish == 0) {
            $this->update(["publish" => 1]);
            return "appear";
        }
        else if ($this->publish == 1) {
            $this->update(["publish" => 0]);
            return "disappear";
        }
        return false;
    }
    public function views(){
        $this->update(["view" => $this->view + 1]);
    }
}
