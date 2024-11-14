<?php

namespace App\Events;

use App\Models\Automation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\ArrayShape;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $user_id;
    public array $data;

    public function __construct($user_id,$data)
    {
        $this->user_id = $user_id;
        $this->data = $data;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->user_id);
    }

    #[ArrayShape(['action' => "mixed", "type" => "mixed", 'data' => "array|\Illuminate\Database\Eloquent\Collection"])] public function broadcastWith(): array
    {
        return ['action' => $this->data["action"],"type" => $this->data["type"],"message" => $this->data["message"]];
    }
}
