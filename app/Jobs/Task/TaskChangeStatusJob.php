<?php

namespace App\Jobs\Task;

use App\Models\Task;
use App\Models\User;
use App\Notifications\Task\TaskChangeStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TaskChangeStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $creatorId, $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($creatorId, Task $task)
    {
        $this->creatorId = $creatorId;
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $creator = User::find($this->creatorId);
        if (!$creator) {
            throw new ItemNotFoundException($creator);
        }

        if($creator->support === 0) {
            $temp = \App::getLocale();

            \App::setLocale(isset($creator->metadata->language) ? $creator->metadata->language : 'de');
            
            try {
                $creator->notify(new TaskChangeStatusNotification($this->task));
            } catch (\Exception $ex) {
                throw new \Exception($ex);
            }

            \App::setLocale($temp);
        }
    }
}
