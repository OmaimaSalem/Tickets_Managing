<?php

namespace Modules\TicketCalendar\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class TicketDeadLineReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $ticket;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assigns = $this->ticket['assigns']->map(function($assign){
            return $assign['email'];
        })->toArray();
        Mail::send('emails.ticket_deadline_reminder', ['ticket' => $this->ticket], function($message) use($assigns){
            $message->to($assigns)->subject($this->ticket['title']." deadline reminder");
        });
    }
}
