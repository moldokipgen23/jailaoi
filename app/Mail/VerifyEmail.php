<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $verifyUrl;
    public $appName;

    public function __construct($name, $verifyUrl)
    {
        $this->name = $name;
        $this->verifyUrl = $verifyUrl;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Verify your email')
            ->view('emails.verify-email');
    }
}
