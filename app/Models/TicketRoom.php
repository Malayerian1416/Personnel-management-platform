<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Clue\StreamFilter\fun;

class TicketRoom extends Model
{
    use HasFactory;
    protected $table = "ticket_rooms";
    protected $fillable = ["subject","user_id"];

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class,"room_id");
    }
    public static function chats($employee_id): \Illuminate\Database\Eloquent\Collection|array
    {
        if (User::UserType() == "admin"){
            return self::query()->with(["tickets" => function($query) use ($employee_id){
                    $query->where("employee_id","=",$employee_id);
            },"tickets.employee","tickets.expert.role"])->orderBy("updated_at")->get();
        }
        else{
            return self::query()->with(["tickets" => function($query) use ($employee_id){
                $query->where("employee_id","=",$employee_id)->where("expert_id","=",Auth::id());
            },"tickets.employee","tickets.expert.role","user" => function($query){
                $query->where("id","=",Auth::id());
            }])->orderBy("updated_at")->get();
        }
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public static function AllTickets(): array
    {
        $result = [];
        $rooms = TicketRoom::query()->with(["tickets.employee.contract.organization","tickets.expert.role","tickets" => function($query){
            $query->where("expert_id","=",Auth::id());
        }])->get();
        $rooms->map(function ($room) use (&$result){
            $result["room$room->id"] = ["id" => $room->id,"subject" => $room->subject,"timestamp" => verta($room->updated_at)->format("Y/m/d"),"employees" => []];
            foreach ($room->tickets as $ticket){
                if (isset($result["room$room->id"]["employees"]["employee$ticket->employee_id"]))
                    $result["room$room->id"]["employees"]["employee$ticket->employee_id"]["messages"][] = [
                        "id" => $ticket->id,
                        "sender" => $ticket->sender,
                        "message" => $ticket->message,
                        "attachment" => $ticket->attachmant,
                        "is_read" => $ticket->is_read,
                        "timestamp" => verta($ticket->updated_at)->format("H:i:s Y/m/d")
                    ];
                else
                    $result["room$room->id"]["employees"]["employee$ticket->employee_id"] = [
                        "id" => $ticket->employee_id,
                        "employee" => "{$ticket->employee->name} ({$ticket->employee->national_code})",
                        "organization" => $ticket->employee->contract->organization->name,
                        "expert" => "{$ticket->expert->name} ({$ticket->expert->role->name})",
                        "messages" => [
                            [
                                "id" => $ticket->id,
                                "sender" => $ticket->sender,
                                "message" => $ticket->message,
                                "attachment" => $ticket->attachmant,
                                "is_read" => $ticket->is_read,
                                "timestamp" => verta($ticket->updated_at)->format("H:i:s Y/m/d")
                            ]
                        ]
                ];
            }
        });
        return $result;
    }
    public static function NewTickets(): \Illuminate\Database\Eloquent\Collection|array
    {
        return TicketRoom::query()->with(["tickets.employee.contract.organization","tickets.expert.role","tickets" => function($query){
            $query->where("expert_id","=",Auth::id());
        }])->whereHas("tickets",function ($query){
            $query->where("is_read","=",0)->where("sender","=","employee");
        })->get();

    }
}
