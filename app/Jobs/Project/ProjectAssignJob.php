<?php

namespace App\Jobs\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Project\ProjectAssignNotification;
use App\Models\Project;

class ProjectAssignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $employees, $project,$current_user,$full_admins;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($employees, Project $project,$current_user)
    {
        $this->employees = $employees;
        $this->project = $project;
        $this->current_user = $current_user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->employees as $employee) {
            $temp = \App::getLocale();


            \App::setLocale(isset($employee->metadata->language) ? $employee->metadata->language : 'de');

            try {
                $employee->notify(new ProjectAssignNotification($this->project,$this->current_user));
            } catch (\Exception $ex) {
                throw new \Exception($ex);
            }

                \App::setLocale($temp);
            }
        }

}
