<?php

namespace App\Listeners;

use App\Events\InvitePolicyMakerEvent;
use App\Mail\NotifyUserEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvitePolicyMakerListener implements ShouldQueue
{
    public function handle(InvitePolicyMakerEvent $event): void
    {
        Log::info('Sending email', [
            'to' => $event->email,
            'subject' => $event->subject,
        ]);
        Mail::to($event->email)->send(new NotifyUserEmail(
            user_name: $event->user_name,
            email: $event->email,
            subject: $event->subject,
            message: $event->message,
            view: $event->view,
            otp: $event->otp
        ));
        Log::info('Email sent successfully');
    }
}
