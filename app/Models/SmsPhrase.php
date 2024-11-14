<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsPhrase extends Model
{
    use HasFactory;
    protected $table = "sms_phrases";
    protected $fillable = ["user_id","category_id","name","text"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SmsPhraseCategory::class,"category_id");
    }
}
