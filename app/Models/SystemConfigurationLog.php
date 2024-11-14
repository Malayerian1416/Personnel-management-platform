<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemConfigurationLog extends Model
{
    use HasFactory;
    protected $table = "system_configurations_log";
    protected $fillable = ["user_id","type"];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public static function byType($type): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query()->with("user")->where("type","=",$type)->orderBy("id","desc")->get()->take(5);
    }
}
