<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $packageName;
    public $amount;
    public $reason;
    public $appName;

    public function __construct($name, $packageName, $amount, $reason = '')
    {
        $this->name = $name;
        $this->packageName = $packageName;
        $this->amount = $amount;
        $this->reason = $reason;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Payment failed')
            ->view('emails.payment-failed');
    }
}
