<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Notifications\Admin\Project\ProjectDueDateNotification;
use Carbon\Carbon;
class DueDateProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:check-due_dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command to get today and tomorrow projects';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $projects = Project::whereDate('due_date',Carbon::today())->get();
        foreach($projects as $project){
            \Notification::send(getRoleUsers('Full Administrator'), new ProjectDueDateNotification($project));
        }
        $projects = Project::whereDate('due_date',Carbon::tomorrow())->get();
        foreach($projects as $project){
            \Notification::send(getRoleUsers('Full Administrator'), new ProjectDueDateNotification($project,'tomorrow'));
        }

    }
}
