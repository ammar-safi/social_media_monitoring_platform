<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitePolicyMakerEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_name;
    public $email;
    public $subject;
    public $message;
    public $view;
    public $otp;

    public function __construct(
        $email,
        $subject,
        $message,
        $otp,
        $user_name = "Policy Maker",
    ) {
        $this->user_name = $user_name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->view = "invite-policy-maker";
        $this->otp = $otp;
    }
}
