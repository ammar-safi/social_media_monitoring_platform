<?php

namespace App\Listeners;

use App\Events\EmailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailListener implements ShouldQueue
{
    public function handle(EmailEvent $event): void
    {
        $recipientEmail = $event->email ?? $event->user?->email;
        $recipientName  = $event->email 
            ? 'Policy Maker' 
            : ($event->user?->first_name ?? 'User');

        if (! $recipientEmail) {
            Log::warning('Email skipped: no recipient found', [
                'event' => EmailEvent::class,
            ]);
            return;
        }

        Log::info('Sending email', [
            'to' => $recipientEmail,
            'subject' => $event->subject,
        ]);

        Mail::send('email.email', [
            'recipientName'  => $recipientName,
            'messageContent' => $event->message,
        ], function ($message) use ($event, $recipientEmail) {
            $message->to($recipientEmail)->subject($event->subject);
        });

        Log::info('Email sent successfully');
    }
}
