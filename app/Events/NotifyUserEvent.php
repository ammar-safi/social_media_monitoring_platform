<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifyUserEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user_name;
    public $email;
    public $subject;
    public $message;

    public function __construct(
        $user_name,
        $email,
        $subject,
        $message

    ) {
        $this->user_name = $user_name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }
}
