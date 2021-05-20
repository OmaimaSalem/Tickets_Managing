<?php

namespace Modules\ProjectComment\Observers;

use Modules\ProjectComment\Entities\ProjectComment;
use Illuminate\Support\Facades\Mail;
use Modules\ProjectComment\Notifications\CommentNotification;

class ProjectCommentObserver
{

    /**
     * Handle the TaskComment "created" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function created(ProjectComment $projectComment)
    {
        \Notification::send($projectComment->project->assigns,new CommentNotification($projectComment->load('project')));
    }

    /**
     * Handle the TaskComment "updated" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function updated(ProjectComment $projectComment)
    {

    }

    /**
     * Handle the TaskComment "deleted" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function deleted(ProjectComment $projectComment)
    {

    }

    /**
     * Handle the TaskComment "restored" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function restored(ProjectComment $projectComment)
    {
        //
    }

    /**
     * Handle the TaskComment "force deleted" event.
     *
     * @param  \App\TaskComment  $taskComment
     * @return void
     */
    public function forceDeleted(ProjectComment $projectComment)
    {
        //
    }
}
