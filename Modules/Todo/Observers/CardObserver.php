<?php

namespace Modules\Todo\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Todo\Entities\Card;


class CardObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Card $card)
    {
        //$card->board;
        $card->tasks;
    }
}
