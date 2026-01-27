<?php

namespace App\Listeners;

use App\Events\EmailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailEvent $event): void
    {
        //TODO 
        \Log::info("--- Sending an email ---");
        \Log::info("--- to " . $event->user?->email . " ---");
        if ($event->email) {
            Mail::send('email.email', [
                'recipientName' => "Policy Maker",
                'messageContent' => $event->message,
            ], function ($message) use ($event) {
                $message->to($event->email)->subject($event->subject);
            });
        } else {
            Mail::send('email.email', [
                'recipientName' => $event->user?->first_name,
                'messageContent' => $event->message,
            ], function ($message) use ($event) {
                $message->to($event->user?->email)->subject($event->subject);
            });
        }
        \Log::info("--- Sending an email ---");
    }
}
