<?php

namespace App\Jobs\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\TicketComment\Entities\TicketComment;

use App\Notifications\Ticket\UpdateReplyTicketNotification;

class UpdateReplyTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ticketComment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TicketComment $ticketComment)
    {
        $this->ticketComment = $ticketComment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $temp = \App::getLocale();

        \App::setLocale(isset($this->ticketComment->ticket->owner->metadata->language) ? $this->ticketComment->ticket->owner->metadata->language : 'de');
        if($this->ticketComment->ticket->owner->support === 0) {
            try {
                $this->ticketComment->ticket->owner->notify(new UpdateReplyTicketNotification($this->ticketComment));
            } catch (\Exception $ex) {
                throw new \Exception($ex);
            }
        }

        \App::setLocale($temp);
    }
}
