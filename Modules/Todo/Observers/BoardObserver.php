<?php

namespace Modules\Todo\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Todo\Entities\Board;


class BoardObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Board $board)
    {
      //
    }
}
