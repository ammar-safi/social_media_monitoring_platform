<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $_user_name;
    public $_email;
    public $_subject;
    public $_message;
    public $_view;
    public $_sender;
    public $_otp;


    /**
     * Create a new message instance.
     */
    public function __construct(
        $user_name,
        $email,
        $subject,
        $message,
        $view,
        $otp = null
    ) {
        $this->_user_name = $user_name;
        $this->_email = $email;
        $this->_subject = $subject;
        $this->_message = $message;
        $this->_view = $view;
        $this->_sender = $this->appName();
        $this->_otp = $otp;

        Log::info("data", [
            "user_name" => $this->_user_name,
            "email" => $this->_email,
            "message" => $this->_message,
            "view" => $this->_view,
            "sender" => $this->_sender,
            $this->_otp = $otp,
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->_subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.' . $this->_view,
            with: [
                "_user_name" => $this->_user_name,
                "_email" => $this->_email,
                "_message" => $this->_message,
                "_sender" => $this->_sender,
                "_otp" => $this->_otp,
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
