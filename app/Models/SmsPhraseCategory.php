<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsPhraseCategory extends Model
{
    use HasFactory;
    protected $table = "sms_phrase_category";
    protected $fillable = ["user_id","name","inactive"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function phrases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SmsPhrase::class,"category_id");
    }
}
