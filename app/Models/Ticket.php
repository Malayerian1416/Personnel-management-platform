<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $table = "tickets";
    protected $fillable = ["employee_id", "expert_id", "room_id", "sender", "subject", "message", "attachment", "is_read"];

    public function room(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TicketRoom::class,"room_id");
    }
    public function employee(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, "employee_id");
    }
    public function expert(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "expert_id");
    }
    public static function LatestTickets(): \Illuminate\Database\Eloquent\Collection|array
    {
        return self::query()->with("employee")->where("expert_id","=",Auth::id())->where("is_read","=",0)
            ->where("sender","=","employee")->orderBy("updated_at","desc")->get();
    }
    public static function TicketMessaging($contract_id,$user_id = null): array
    {
        if ($user_id)
            $users = User::query()->with(["contracts"])->Where("id","=",$user_id)->get();
        $result["message"]["users"] = $users ?? User::RecipientTicketing($contract_id);
        $result["message"]["data"]["message"] = "تیکت پشتیبانی جدید دریافت شد";
        $result["message"]["data"]["action"] = "";
        $result["message"]["data"]["type"] = "ticket";
        return $result;
    }

}
