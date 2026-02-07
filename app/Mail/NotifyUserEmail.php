<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user_name;
    public $email;
    public $subject;
    public $message;
    public $view;
    public $sender;


    /**
     * Create a new message instance.
     */
    public function __construct(
        $user_name,
        $email,
        $subject,
        $message,
        $view
    ) {
        $this->user_name = $user_name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->view = $view;
        $this->sender = $this->appName();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.' . $this->view,
            with: [
                "user_name" => $this->user_name,
                "email" => $this->email,
                "subject" => $this->subject,
                "message" => $this->message,
                "view" => $this->view,
                "sender" => $this->sender,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function appName(): string
    {
        $app_name_camel_case = config('app.name');
        $app_name_split_in_array = preg_split(
            "/(?<=[a-z])(?=[A-Z])/",
            $app_name_camel_case
        );
        $app_name = implode(' ', $app_name_split_in_array);
        $app_name = ucfirst($app_name);
        return $app_name;
    }
}
