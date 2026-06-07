<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArtistApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $artistName;
    public $loginUrl;
    public $appName;

    public function __construct($name, $artistName)
    {
        $this->name = $name;
        $this->artistName = $artistName;
        $this->loginUrl = url('/user/login');
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Your artist application is approved!')
            ->view('emails.artist-approved');
    }
}
