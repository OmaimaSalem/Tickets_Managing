<?php

namespace App\Jobs\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Ticket;

use App\Events\TicketStatusChanged;
use App\Notifications\Ticket\TicketChangeStatusNotification;

class TicketChangeStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ticket;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
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
        $temp = \App::getLocale();

        \App::setLocale(isset($this->ticket->owner->first()->metadata->language) ? $this->ticket->owner->first()->metadata->language : 'de');

        try {
            // $this->ticket->owner->notify(new TicketChangeStatusNotification($this->ticket));
            if($this->ticket->owner->count() > 0) {
                foreach($this->ticket->owner as $owner){
                    event(new TicketStatusChanged($owner->id,$this->ticket,'owner'));
                    if($owner->support === 0) {
                        $owner->notify(new TicketChangeStatusNotification($this->ticket));
                    }
                }
            }






            if($this->ticket->assigns->count() > 0) {
                foreach($this->ticket->assigns as $assign){
                    if($assign->support === 0) {
                        $assign->notify(new TicketChangeStatusNotification($this->ticket));
                    }
                }
            }


        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }

        \App::setLocale($temp);
    }
}
