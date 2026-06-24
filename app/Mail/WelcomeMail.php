<?php

namespace App\Mail;

use App\Models\Social_Link;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $appName;
    public $socialLinks;

    public function __construct($name)
    {
        $this->name = $name;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
        $this->socialLinks = Social_Link::where('status', 1)->get();
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Welcome aboard!')
            ->view('emails.welcome');
    }
}
