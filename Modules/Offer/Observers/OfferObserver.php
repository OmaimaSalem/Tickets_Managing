<?php

namespace Modules\Offer\Observers;


use App\Models\Setting;
use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Offer\Entities\Offer;


class OfferObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Offer $offer)
    {
        $offer->offer_items;
        $offer->client;
        $offer->contract;
        $offer->creator;
    }

    /**
     * Handle the offer "creating" event.
     *
     * @param  \App\Offer  $offer
     * @return void
     */
    public function creating(Offer $offer)
    {
        $offerSetting = Setting::where('entity', 'offer')
                ->where('current', true)
                ->orderBy('created_at', 'desc')->first();
        if ($offerSetting) {
            $offer->setting_id = $offerSetting->id;
            $offer->number = $offerSetting->key . sprintf("%08d", $offerSetting->last_number + 1);
            $offerSetting->last_number = sprintf("%08d", $offerSetting->last_number + 1);
            $offerSetting->updated_by = isset(auth()->user()->id) ? auth()->user()->id  : 1; // admin
            $offerSetting->save();
        }
    }

    public function created(Offer $offer)
    {
        $this->activityLog->addToLog('Create Offer Number: '. $offer->number, $offer->client_id, null, null, null, null, null, null,$offer->id);
    }

    public function updated(Offer $offer)
    {
        //
    }

    public function deleted(Offer $offer)
    {
        //
    }

    public function restored(Offer $offer)
    {
        //
    }

    public function forceDeleted(Offer $offer)
    {
        //
    }
}
