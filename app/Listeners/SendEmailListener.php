<?php

namespace App\Listeners;

use App\Events\EmailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailListener
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
        \Log::info("--- Sending an email ---");
        \Log::info("--- to " . $event->user?->first_name ." ---");
        Mail::send('email.email', [
            'recipientName' => $event->user?->first_name,
            'messageContent' => $event->message,
        ], function ($message) use ($event) {
            $message->to($event->user?->email)->subject($event->subject);
        });
        \Log::info("--- Sending an email ---");

    }
}
