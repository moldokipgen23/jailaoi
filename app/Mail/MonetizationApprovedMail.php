<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonetizationApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $appName;

    public function __construct($name)
    {
        $this->name = $name;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Your monetization application is approved!')
            ->view('emails.monetization-approved');
    }
}
