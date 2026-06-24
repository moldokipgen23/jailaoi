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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject($this->details['title'])
            ->view($this->details['view']);

        if (isset($this->details['attachment']) && file_exists($this->details['attachment'])) {
            $mail->attach($this->details['attachment'], [
                'as' => $this->details['attachment_name'] ?? 'invoice.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
