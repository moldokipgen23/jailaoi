<?php

namespace App\Mail;

use App\Models\Social_Link;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $resetUrl;
    public $appName;
    public $socialLinks;

    public function __construct($name, $resetUrl)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
        $this->socialLinks = Social_Link::where('status', 1)->get();
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Reset your password')
            ->view('emails.forgot-password');
    }
}
