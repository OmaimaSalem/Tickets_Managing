<?php

namespace App\Mail\Attendees;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttendReviewMail extends Mailable
{
    use Queueable, SerializesModels;
    private $attend;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($attend)
    {
        $this->attend = $attend;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.templates.attend-review')->with(['attend' => $this->attend]);
    }
}
