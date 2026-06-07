<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MigratedArtistWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $resetUrl;
    public $appName;

    public function __construct($name, $resetUrl)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Welcome! Set your password')
            ->view('emails.migrated-artist-welcome');
    }
}
