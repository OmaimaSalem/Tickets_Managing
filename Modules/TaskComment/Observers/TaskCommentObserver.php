<?php

namespace Modules\TaskComment\Observers;

use Modules\TaskComment\Entities\TaskComment;
use Modules\Activity\Http\Controllers\ActivityController;
use Modules\TaskComment\Events\TaskCommentAdded;
use Modules\TaskComment\Jobs\TaskCommentJob;
use Illuminate\Support\Facades\Mail;
use App\Notifications\Admin\Task\CommentAddedNotification;

class TaskCommentObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }
    /**
     * Handle the TaskComment "created" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function created(TaskComment $taskComment)
    {
        $taskComment->creator;
        $taskComment->updater;

        $ticket_id = null;
        if ($taskComment->task->ticket) {
            $ticket_id = $taskComment->task->ticket->id;
        }


        \Notification::send(getRoleUsers('Full Administrator'),new CommentAddedNotification($taskComment->task,auth()->user()));

        TaskCommentJob::dispatch($taskComment);
        if($taskComment->task->project) {
            $this->activityLog->addToLog('Create Comment on Task: '.$taskComment->task->name, null, $taskComment->task->project->id, $ticket_id, $taskComment->task->id);
        }else if($taskComment->task->ticket && $taskComment->task->ticket->project){
            $this->activityLog->addToLog('Create Comment on Task: '.$taskComment->task->name, null, $taskComment->task->ticket->project->id, $ticket_id, $taskComment->task->id);
        }else {
            $this->activityLog->addToLog('Create Comment on Task: '.$taskComment->task->name, null, null, $ticket_id, $taskComment->task->id);
        }
    }

    /**
     * Handle the TaskComment "updated" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function updated(TaskComment $taskComment)
    {
        $taskComment->creator;
        $taskComment->updater;

        $ticket_id = null;
        if ($taskComment->task->ticket) {
            $ticket_id = $taskComment->task->ticket->id;
        }


//         TicketReplyJob::dispatch($ticketReply,$oldReplies, $filesPaths, $MailsArr, $ticket,$Cc_arr,$Bcc_ar);

        $this->activityLog->addToLog('Update Comment on Task: '.$taskComment->task->name, null, $taskComment->task->project->id, $ticket_id, $taskComment->task->id);
    }

    /**
     * Handle the TaskComment "deleted" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function deleted(TaskComment $taskComment)
    {
        $taskComment->creator;
        $taskComment->updater;

        $ticket_id = null;
        if ($taskComment->task->ticket) {
            $ticket_id = $taskComment->task->ticket->id;
        }

        $this->activityLog->addToLog('Delete Comment on Task: '.$taskComment->task->name, null, $taskComment->task->project->id, $ticket_id, $taskComment->task->id);
    }

    /**
     * Handle the TaskComment "restored" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function restored(TaskComment $taskComment)
    {
        //
    }

    /**
     * Handle the TaskComment "force deleted" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function forceDeleted(TaskComment $taskComment)
    {
        //
    }
}
