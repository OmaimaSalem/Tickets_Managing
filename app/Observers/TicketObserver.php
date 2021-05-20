<?php

namespace App\Observers;

use App\Jobs\Task\TaskAssignJob;
use App\Jobs\Ticket\TicketAssignJob;
use App\Jobs\Ticket\TicketChangeStatusJob;
use App\Jobs\Ticket\TicketCreatedJob;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\Admin\Ticket\AssignTicketNotification;
use Modules\Activity\Http\Controllers\ActivityController;
use App\Events\TicketStatusChanged;
use App\Notifications\Admin\Ticket\StatusTicketNotification;
use App\Notifications\Admin\Ticket\UpdateTicketNotification;
use App\Notifications\Admin\Ticket\DeleteTicketNotification;
use Illuminate\Http\Request;

class TicketObserver
{
    private $activityLog;

    public function __construct(Request $request, ActivityController $activityLog)
    {
        $this->input = $request->all();
        $this->activityLog = $activityLog;
    }

    public function retrieved(Ticket $ticket)
    {
        $ticket->project;
    }

    /**
     * Handle the ticket "creating" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function creating(Ticket $ticket)
    {
        $ticketSetting = Setting::where('entity', 'ticket')
            ->where('current', true)
            ->orderBy('created_at', 'desc')->first();

        if ($ticketSetting) {
            $ticket->setting_id = $ticketSetting->id;
            $ticket->number = $ticketSetting->key . sprintf("%08d", $ticketSetting->last_number + 1);

            $ticketSetting->last_number = sprintf("%08d", $ticketSetting->last_number + 1);
            $ticketSetting->updated_by = isset(auth()->user()->id) ? auth()->user()->id : 1; // admin
            $ticketSetting->save();
        }
    }

    /**
     * Handle the ticket "created" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function created(Ticket $ticket)
    {
        if (isset($ticket) && isset($this->input['manager_id']) ) {
            $assigns = User::find($this->input['manager_id']);
            TicketAssignJob::dispatch($this->input['manager_id'], $ticket);
            \Notification::send(getRoleUsers('Full Administrator'), new AssignTicketNotification($ticket, auth()->user(), $assigns));
        }

        if ($ticket->project_id) {
            $this->activityLog->addToLog('Create ticket: ' . $ticket->name, null, $ticket->project->id, $ticket->id);
        } else {
            $this->activityLog->addToLog('Create ticket: ' . $ticket->name, null, null, $ticket);
        }

        // TicketCreatedJob::dispatch($ticket);

        // $ticket->project->owner;

        // $ticketSetting = Setting::find($ticket->setting_id);

        // $this->activityLog->addToLog('Create ticket: '.$ticket->name, $ticket->project->owner->id, $ticket->project->id, $ticket->id);
    }

    /**
     * Handle the ticket "updating" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function updating(Ticket $ticket)
    {
        if ($ticket->isDirty('status_id') && ($ticket->status_id == 4 || $ticket->status_id == 3)) {
            // status is changed && new status is done or in-progress
            TicketChangeStatusJob::dispatch($ticket);
        }

        if ($ticket->isDirty('status_id')) {
            \Notification::send(getRoleUsers('Full Administrator'), new StatusTicketNotification($ticket, auth()->user()));
        }


        if ($ticket->isDirty('name') || $ticket->isDirty('estimated_time') || $ticket->isDirty('description')) {
            \Notification::send(getRoleUsers('Full Administrator'), new UpdateTicketNotification($ticket, auth()->user()));
        }

    }

    /**
     * Handle the ticket "updated" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        if ($ticket->project) {
            $this->activityLog->addToLog('Create ticket: ' . $ticket->name, null, $ticket->project->id, $ticket->id);
        } else {
            $this->activityLog->addToLog('Create ticket: ' . $ticket->name, null, null, $ticket->id);
        }
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {

        \Notification::send(getRoleUsers('Full Administrator'), new DeleteTicketNotification($ticket->name, auth()->user()));

        if ($ticket->project) {
            $this->activityLog->addToLog('Delete ticket: ' . $ticket->name, null, $ticket->project->id, $ticket->id);
        } else {
            $this->activityLog->addToLog('Delete ticket: ' . $ticket->name, null, null, $ticket->id);
        }
    }

    /**
     * Handle the ticket "restored" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the ticket "force deleted" event.
     *
     * @param \App\Ticket $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }
}
