<?php

namespace Modules\TicketComment\Observers;

use App\Jobs\Ticket\CommentTicketJob;
use App\Jobs\Ticket\ReplyTicketJob;
use App\Jobs\Ticket\UpdateReplyTicketJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Modules\Activity\Http\Controllers\ActivityController;
use Modules\TicketComment\Entities\TicketComment;
use App\Notifications\Admin\Ticket\CommentAddedNotification;


class TicketCommentObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(TicketComment $ticketComment)
    {
        $ticketComment->creator;
    }


    /**
     * Handle the TicketComment "created" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function created(TicketComment $ticketComment)
    {


        \Notification::send(getRoleUsers('Full Administrator'),new CommentAddedNotification($ticketComment->ticket,auth()->user()));

        // $description = Request::input('mail');
        // // dd($description);
        // /*$unsortConversations = $ticketComment->ticket->conversations;
        // $sortConversations = $unsortConversations->sortByDesc('created_at');
        // foreach($sortConversations as $conversation) {
        //     $description .= $conversation->conversation . '<hr />';
        // }*/
        // $subject = Request::input('subject');
        // $user = Auth::user();
        // $ticketComment->creator;
        // $ticketComment->updater;

        // // if($ticketComment->ticket->project) {
        // //     $this->activityLog->addToLog('Create Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
        // // } else {
        //     $this->activityLog->addToLog('Create Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->owner->first()->id, null, $ticketComment->ticket->id);
        // // }


        // if ($ticketComment->send_mail) {
        //     ReplyTicketJob::dispatch($ticketComment, $user, $description, $subject);
        //     if($ticketComment->ticket->project) {
        //         $this->activityLog->addToLog('Sending an email for replying on Ticket: '.$ticketComment->ticket->name.', to: '.$ticketComment->ticket->owner->first()->email.', cc: '.$ticketComment->ticket->mails->pluck('email'), $ticketComment->ticket->owner->first()->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
        //     } else {
        //         $this->activityLog->addToLog('Sending an email for replying on Ticket: '.$ticketComment->ticket->name.', to: '.$ticketComment->ticket->owner->first()->email.', cc: '.$ticketComment->ticket->mails->pluck('email'), $ticketComment->ticket->owner->first()->id, null, $ticketComment->ticket->id);
        //     }

        // } else {
        //     // CommentTicketJob::dispatch($ticketComment);
        // }
    }

    /**
     * Handle the TicketComment "updated" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function updated(TicketComment $ticketComment)
    {
        $ticketComment->creator;
        $ticketComment->updater;

        // if($ticketComment->ticket->project) {
        //     $this->activityLog->addToLog('Update Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
        // } else {
            $this->activityLog->addToLog('Update Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->owner->id, null, $ticketComment->ticket->id);
        // }

        if ($ticketComment->send_mail) {
            UpdateReplyTicketJob::dispatch($ticketComment);
            // if($ticketComment->ticket->project) {
            //     $this->activityLog->addToLog('Sending an email for the updating reply on Ticket: '.$ticketComment->ticket->name.', to: '.$ticketComment->ticket->project->owner->email.', cc: '.$ticketComment->ticket->mails->pluck('email'), $ticketComment->ticket->project->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
            // } else {
                $this->activityLog->addToLog('Sending an email for the updating reply on Ticket: '.$ticketComment->ticket->name.', to: '.$ticketComment->ticket->project->owner->email.', cc: '.$ticketComment->ticket->mails->pluck('email'), $ticketComment->ticket->owner->id, null, $ticketComment->ticket->id);
            // }

        }
    }

    /**
     * Handle the TicketComment "deleted" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function deleted(TicketComment $ticketComment)
    {
        $ticketComment->creator;
        $ticketComment->updater;

        if($ticketComment->ticket->project) {
            $this->activityLog->addToLog('Delete Comment on Ticket: '.$ticketComment->ticket->name, $ticketComment->ticket->owner->id, $ticketComment->ticket->project->id, $ticketComment->ticket->id);
        }
    }

    /**
     * Handle the TicketComment "restored" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function restored(TicketComment $ticketComment)
    {
        //
    }

    /**
     * Handle the TicketComment "force deleted" event.
     *
     * @param  \App\TicketComment  $ticketComment
     * @return void
     */
    public function forceDeleted(TicketComment $ticketComment)
    {
        //
    }
}
