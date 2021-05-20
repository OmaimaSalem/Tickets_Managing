<?php

namespace Modules\ItemCategory\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\ItemCategory\Entities\ItemCategory;



class ItemCategoryObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(ItemCategory $category)
    {
        $category->items;
    }

    public function created(ItemCategory $category)
    {
        //
    }

    public function updated(ItemCategory $category)
    {
        //
    }

    public function deleted(ItemCategory $category)
    {
        //
    }

    public function restored(ItemCategory $category)
    {
        //
    }

    public function forceDeleted(ItemCategory $category)
    {
        //
    }
}
