<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RenewalReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $packageName;
    public $expiryDate;
    public $appName;

    public function __construct($name, $packageName, $expiryDate)
    {
        $this->name = $name;
        $this->packageName = $packageName;
        $this->expiryDate = $expiryDate;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Subscription renewal reminder')
            ->view('emails.renewal-reminder');
    }
}
