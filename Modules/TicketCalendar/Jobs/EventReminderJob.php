<?php

namespace Modules\TicketCalendar\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class EventReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $event;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assigns = $this->event['ticket']['assigns']->map(function($assign){
            return $assign['email'];
        })->toArray();
        Mail::send('emails.event_reminder', ['event' => $this->event], function($message) use($assigns){
            $message->to($assigns)->subject($this->event['title']." event reminder");
        });
    }
}
