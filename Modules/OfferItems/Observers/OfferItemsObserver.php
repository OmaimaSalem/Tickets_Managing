<?php

namespace Modules\OfferItems\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\OfferItems\Entities\OfferItems;



class OfferItemsObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(OfferItems $offerItems)
    {
        //
    }

    public function created(OfferItems $offerItems)
    {
        //
    }

    public function updated(OfferItems $offerItems)
    {
        //
    }

    public function deleted(OfferItems $offerItems)
    {
        //
    }

    public function restored(OfferItems $offerItems)
    {
        //
    }

    public function forceDeleted(OfferItems $offerItems)
    {
        //
    }
}
