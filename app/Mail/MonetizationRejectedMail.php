<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonetizationRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $reason;
    public $appName;

    public function __construct($name, $reason)
    {
        $this->name = $name;
        $this->reason = $reason;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Monetization application update')
            ->view('emails.monetization-rejected');
    }
}
