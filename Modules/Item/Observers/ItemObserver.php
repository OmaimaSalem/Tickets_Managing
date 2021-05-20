<?php

namespace Modules\Item\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Item\Entities\Item;



class ItemObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Item $item)
    {
        // $item->category;
    }
    /**
     * Handle the TicketComment "created" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function created(Item $item)
    {
        // $ticketComment->creator;
        // $ticketComment->updater;

        // $this->activityLog->addToLog('Create Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
        
        // if ($ticketComment->send_mail) {
        //     ReplyTicketJob::dispatch($ticketComment, $user, $description);
        //     $this->activityLog->addToLog('Sending an email for replying on Ticket: '.$ticketComment->ticket->name.', to: '.$ticketComment->ticket->project->owner->email.', cc: '.$ticketComment->ticket->mails->pluck('email'), $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
        // }
    }

    /**
     * Handle the TicketComment "updated" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function updated(Item $item)
    {
        // $ticketComment->creator;
        // $ticketComment->updater;

        // $this->activityLog->addToLog('Update Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
   
        // if ($ticketComment->send_mail) {
        //     UpdateReplyTicketJob::dispatch($ticketComment);
        //     $this->activityLog->addToLog('Sending an email for the updating reply on Ticket: '.$ticketComment->ticket->name.', to: '.$ticketComment->ticket->project->owner->email.', cc: '.$ticketComment->ticket->mails->pluck('email'), $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);

        // }
    }

    /**
     * Handle the TicketComment "deleted" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function deleted(Item $item)
    {
        // $ticketComment->creator;
        // $ticketComment->updater;
    
        // $this->activityLog->addToLog('Delete Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
    }

    /**
     * Handle the TicketComment "restored" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function restored(Item $item)
    {
        //
    }

    /**
     * Handle the TicketComment "force deleted" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function forceDeleted(Item $item)
    {
        //
    }
}
