<?php

namespace Modules\Todo\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Todo\Entities\Task;


class TaskObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Task $task)
    {
        //$task->card;
    }
}
