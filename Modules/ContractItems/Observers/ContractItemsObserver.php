<?php

namespace Modules\ContractItems\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\ContractItems\Entities\ContractItems;



class ContractItemsObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(ContractItems $contractItems)
    {
        //
    }

    public function created(ContractItems $contractItems)
    {
        //
    }

    public function updated(ContractItems $contractItems)
    {
        //
    }

    public function deleted(ContractItems $contractItems)
    {
        //
    }

    public function restored(ContractItems $contractItems)
    {
        //
    }

    public function forceDeleted(ContractItems $contractItems)
    {
        //
    }
}
