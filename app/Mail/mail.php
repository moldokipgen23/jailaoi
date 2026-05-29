<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class mail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $view;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $view)
    {
        $this->details = $details;
        $this->view = $view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->details['title'])
            ->view($this->view);
    }
}
