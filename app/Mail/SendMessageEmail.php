<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageContent;
    public $recipientName;

    public function __construct($messageContent, $recipientName)
    {
        $this->messageContent = $messageContent;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        return $this->from('qosaykorde@gmail.com')->subject('new message')->view('email.email')
            ->with([
                'messageContent' => $this->messageContent,
                'recipientName' => $this->recipientName,

            ]);
    }
}
