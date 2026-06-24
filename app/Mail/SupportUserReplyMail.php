<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportUserReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $userName;
    public $subject;
    public $message;
    public $ticketId;
    public $appName;

    public function __construct($userName, $subject, $message, $ticketId)
    {
        $this->userName = $userName;
        $this->subject = $subject;
        $this->message = $message;
        $this->ticketId = $ticketId;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject('[' . $this->appName . '] User replied to ticket #' . $this->ticketId . ': ' . $this->subject)
            ->view('emails.support-user-reply');
    }
}
