<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SystemBackup extends Model
{
    use HasFactory;
    protected $table = "system_backups";
    protected $fillable = ["user_id","name","type","filename","status","exception"];
    protected $appends = ["download"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function getDownloadAttribute(): string
    {
        if ($this->filename && Storage::disk("system_backups")->exists($this->filename))
            return Str::replace("/","@",$this->filename);
        return "";
    }
}
