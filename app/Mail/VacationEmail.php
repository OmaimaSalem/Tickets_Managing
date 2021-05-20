<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VacationEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $vacation;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vacation)
    {
        $this->vacation = $vacation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.templates.vacation')->with(['vacation' => $this->vacation]);
    }
}
