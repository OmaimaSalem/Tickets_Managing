<?php

namespace Modules\Category\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\Category\Entities\Category;



class CategoryObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(Category $category)
    {
        //
    }

    public function created(Category $category)
    {
        //
    }

    public function updated(Category $category)
    {
        //
    }

    public function deleted(Category $category)
    {
        //
    }

    public function restored(Category $category)
    {
        //
    }

    public function forceDeleted(Category $category)
    {
        //
    }
}
