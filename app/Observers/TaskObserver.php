<?php

namespace App\Observers;

use App\Exceptions\ItemNotUpdatedException;
use App\Jobs\Task\TaskAssignJob;
use App\Jobs\Task\TaskChangeStatusJob;
use App\Models\Task;
use Modules\Activity\Http\Controllers\ActivityController;
use \Illuminate\Http\Request;
use DB;
use App\Notifications\Admin\Task\StatusTaskNotification;
use App\Notifications\Admin\Task\UpdateTaskNotification;
use App\Notifications\Admin\Task\DeleteTaskNotification;
use App\Notifications\Admin\Task\AssigTaskNotification;
use App\Models\User;
class TaskObserver
{
    private $input;
    private $activityLog;

    public function __construct(Request $request, ActivityController $activityLog)
    {
        $this->input = $request->all();
        $this->activityLog = $activityLog;
    }


    public function retrieved(Task $task)
    {
        $task->creator;
    }


    /**
     * Handle the task "created" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function created(Task $task)
    {
        if (isset($this->input['responsible_id'])) {
            TaskAssignJob::dispatch($this->input['responsible_id'], $task);
            $assign = User::find($this->input['responsible_id']);
            \Notification::send(getRoleUsers('Full Administrator'),new AssigTaskNotification($task,auth()->user(),$assign->name));
        }

        $ticket_id = null;
        if ($task->ticket) {
            $ticket_id = $task->ticket->id;
        }
        if($task->project_id) {
            $this->activityLog->addToLog('Create task: '.$task->name, null, $task->project->id, $ticket_id, $task->id);
        } else {
            $this->activityLog->addToLog('Create task: '.$task->name, null, null, $ticket_id, $task->id);
        }
    }

    /**
     * Handle the task "updating" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function updating(Task $task)
    {

        if($task->isDirty('responsible_id')){
            $assign = User::find($this->input['responsible_id']);
            TaskAssignJob::dispatch($assign->id, $task);            
            \Notification::send(getRoleUsers('Full Administrator'),new AssigTaskNotification($task,auth()->user(),$assign->name));
        }


        if($task->isDirty('status_id') && ($task->status_id == 2 || $task->status_id == 3)){
            // status is changed && new status is pending or in-progress
            if ($task->ticket) {
                $task->ticket->status_id = $task->status_id;
                $task->ticket->save();
            }

        }


        // if($task->isDirty('responsible_id')){
        //     $assign = User::find($this->input['responsible_id']);
        //     if($assign){
        //         \Notification::send(getRoleUsers('Full Administrator'),new AssigTaskNotification($task,auth()->user(),$assign->name));
        //     }
        // }



        if($task->isDirty('status_id')){
          \Notification::send(getRoleUsers('Full Administrator'),new StatusTaskNotification($task,auth()->user()));
        }






    }

    /**
     * Handle the task "updated" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function updated(Task $task)
    {
        if (isset($this->input['responsible_id'])) {
            // TaskAssignJob::dispatch($this->input['responsible_id'], $task);
        } else if(isset($this->input['status_id'])) {
           if($task->creator){
             TaskChangeStatusJob::dispatch($task->creator->id, $task);
           } 
        }

        $task->project;
        $task->responsible;
        $task->task_status;
        $task->deadline;

        $ticket_id = null;
        if ($task->ticket) {
            $ticket_id = $task->ticket->id;
        }



        if($task->isDirty('name') || $task->isDirty('description') || $task->isDirty('estimated_time') || $task->isDirty('priority') ){
            \Notification::send(getRoleUsers('Full Administrator'),new UpdateTaskNotification($task,auth()->user()));
        }



        //$this->activityLog->addToLog('Update task: '.$task->name, null, $task->project->id, $ticket_id, $task->id);
    }

    /**
     * Handle the task "deleted" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function deleted(Task $task)
    {
        $ticket_id = null;
        if ($task->ticket) {
            $ticket_id = $task->ticket->id;
        }
        if($task->project_id) {
            $this->activityLog->addToLog('Delete task: '.$task->name, null, $task->project->id, $ticket_id, $task->id);
        } else {
            $this->activityLog->addToLog('Delete task: '.$task->name, null, null, $ticket_id, $task->id);
        }

        \Notification::send(getRoleUsers('Full Administrator'),new DeleteTaskNotification($task->name,auth()->user()));

        // $this->activityLog->addToLog('Delete task: '.$task->name, null, $task->project->id, $ticket_id, $task->id);
    }

    /**
     * Handle the task "restored" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function restored(Task $task)
    {
        //
    }

    /**
     * Handle the task "force deleted" event.
     *
     * @param  \App\Task  $task
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}
