<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\User;
use App\Jobs\Project\ProjectAssignJob;
use \Illuminate\Http\Request;
use Modules\Activity\Http\Controllers\ActivityController;
use App\Events\ProjectAssignNotification;
use App\Notifications\Admin\Project\ProjectAssign;
use Carbon\Carbon;
use App\Notifications\Admin\Project\ProjectStatusNotification;
use App\Notifications\Admin\Project\ProjectDueDateNotification;
use App\Notifications\Admin\Project\ProjectDeleteNotification;
use App\Notifications\Admin\Project\ProjectOwnerAssign;







class ProjectObserver
{
    private $input;
    private $activityLog;

    public function __construct(Request $request, ActivityController $activityLog)
    {
        $this->input = $request->all();
        $this->activityLog = $activityLog;
    }

    public function retrieved(Project $project)
    {
        $project->owner;
        $project->creator;
    }

    /**
     * Handle the project "created" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function created(Project $project)
    {

        $project->owner()->sync(request()->owner);
        $project->project_status()->associate([
            'id'   => request()->status['id'],
            'name' => request()->status['name'],
            'created_by' => auth()->user()->id,
        ]);

        if (isset($this->input['project_assign']) && $this->input['project_assign']) {
            $employees = User::find($this->input['project_assign']);
            $project->assigns()->attach($employees);

            ProjectAssignJob::dispatch($employees, $project, Auth()->user());
        }

        $project->owner;

        $this->activityLog->addToLog('Create project: '.$project->name, 0, $project->id);
    }

    /**
     * Handle the project "updated" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function updated(Project $project)
    {

        $new_owners = $project->owner()->sync(request()->owner);
        $new_owners = User::find($new_owners['attached']);

        if(count($new_owners) > 0){
            \Notification::send(getRoleUsers('Full Administrator'), new ProjectOwnerAssign($project,auth()->user(),$new_owners));
        }


        if (isset($this->input['project_assign'])) {
            $employees = User::find($this->input['project_assign']);
            $assigns = $project->assigns->pluck('id')->toArray();
            $new_assigns = $project->assigns()->sync($employees);
            $new_assigns = User::find($new_assigns['attached']);

            // $project->project_status()->associate([
            //     'id'   => request()->status['id'],
            //     'name' => request()->status['name'],
            //     'created_by' => auth()->user()->id,
            // ]);

            ProjectAssignJob::dispatch($employees, $project,auth()->user());
            if(count($new_assigns) > 0){
                \Notification::send(getRoleUsers('Full Administrator'), new ProjectAssign($project,auth()->user(),$new_assigns));
            }
        }


        if($project->isDirty('status_id')){
            \Notification::send(getRoleUsers('Full Administrator'), new ProjectStatusNotification($project));
        }

        if($project->isDirty('due_date')){
            \Notification::send(getRoleUsers('Full Administrator'), new ProjectDueDateNotification($project));
        }


        $project->owner;

        $this->activityLog->addToLog('Update project: '.$project->name, 0, $project->id);
    }

    /**
     * Handle the project "deleted" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function deleted(Project $project)
    {
        // $project->assigns()->detach();
        // $project->owner()->detach();

        \Notification::send(getRoleUsers('Full Administrator'), new ProjectDeleteNotification($project));
        $this->activityLog->addToLog('Delete project: '.$project->name, 0, $project->id);
    }

    /**
     * Handle the project "restored" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function restored(Project $project)
    {
        //
    }

    /**
     * Handle the project "force deleted" event.
     *
     * @param  \App\Project  $project
     * @return void
     */
    public function forceDeleted(Project $project)
    {
        //
    }
}
