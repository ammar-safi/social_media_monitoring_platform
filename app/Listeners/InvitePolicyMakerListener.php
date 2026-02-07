<?php

namespace App\Listeners;

use App\Events\InvitePolicyMakerEvent;
use App\Mail\NotifyUserEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvitePolicyMakerListener
{
    public function handle(InvitePolicyMakerEvent $event): void
    {
        Log::info('Sending email', [
            'to' => $event->email,
            'subject' => $event->subject,
        ]);
        Mail::to($event->email)->send(new NotifyUserEmail(
            $event->user_name,
            $event->email,
            $event->subject,
            $event->message,
            $event->view
        ));
        Log::info('Email sent successfully');
    }
}
