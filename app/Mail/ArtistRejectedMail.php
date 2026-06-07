<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ArtistRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $artistName;
    public $adminNote;
    public $appName;

    public function __construct($name, $artistName, $adminNote = '')
    {
        $this->name = $name;
        $this->artistName = $artistName;
        $this->adminNote = $adminNote;
        $this->appName = function_exists('App_Name') ? App_Name() : 'JailaOi';
    }

    public function build()
    {
        return $this->subject($this->appName . ' — Update on your artist application')
            ->view('emails.artist-rejected');
    }
}
