<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $subject;
    public $reply;
    public $ticketUrl;
    public $appName;

    public function __construct($name, $subject, $reply, $ticketUrl)
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->reply = $reply;
        $this->ticketUrl = $ticketUrl;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Support ticket update: ' . $this->subject)
            ->view('emails.support-reply');
    }
}
