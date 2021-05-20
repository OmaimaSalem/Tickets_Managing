<?php

namespace Modules\Contract\Observers;


use App\Models\Setting;
use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Contract\Entities\Contract;



class ContractObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Contract $contract)
    {
        $contract->contract_items;
        $contract->client;
        $contract->creator;
    }

    /**
     * Handle the contract "creating" event.
     *
     * @param  \App\Contract  $contract
     * @return void
     */
    public function creating(Contract $contract)
    {
        $contractSetting = Setting::where('entity', 'contract')
                ->where('current', true)
                ->orderBy('created_at', 'desc')->first();

        if ($contractSetting) {
            $contract->setting_id = $contractSetting->id;
            $contract->number = $contractSetting->key . sprintf("%08d", $contractSetting->last_number + 1);

            $contractSetting->last_number = sprintf("%08d", $contractSetting->last_number + 1);
            $contractSetting->updated_by = isset(auth()->user()->id) ? auth()->user()->id  : 1; // admin
            $contractSetting->save();
        }
    }

    public function created(Contract $contract)
    {
        //
    }

    public function updated(Contract $contract)
    {
        //
    }

    public function deleted(Contract $contract)
    {
        //
    }

    public function restored(Contract $contract)
    {
        //
    }

    public function forceDeleted(Contract $contract)
    {
        //
    }
}
