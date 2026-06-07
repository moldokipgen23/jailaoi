<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawalPaidMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $amount;
    public $paymentMethod;
    public $reference;
    public $appName;

    public function __construct($name, $amount, $paymentMethod, $reference = '')
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->reference = $reference;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Withdrawal paid: ' . number_format($this->amount, 2))
            ->view('emails.withdrawal-paid');
    }
}
