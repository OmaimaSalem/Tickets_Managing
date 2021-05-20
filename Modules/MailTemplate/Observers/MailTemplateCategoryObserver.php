<?php

namespace Modules\MailTemplate\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\MailTemplate\Entities\MailTemplateCategory;



class MailTemplateCategoryObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(MailTemplateCategory $category)
    {
      $category->templates;
    }
    /**
     * Handle the MailTemplateCategory "created" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplateCategory  $category
     * @return void
     */
    public function created(MailTemplateCategory $category)
    {
        //
    }

    /**
     * Handle the MailTemplateCategory "updated" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplateCategory  $category
     * @return void
     */
    public function updated(MailTemplateCategory $category)
    {
        //
    }

    /**
     * Handle the MailTemplateCategory "deleted" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplateCategory  $category
     * @return void
     */
    public function deleted(MailTemplateCategory $category)
    {
        //
    }

    /**
     * Handle the MailTemplateCategory "restored" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplateCategory  $category
     * @return void
     */
    public function restored(MailTemplateCategory $category)
    {
        //
    }

    /**
     * Handle the MailTemplateCategory "force deleted" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplateCategory  $category
     * @return void
     */
    public function forceDeleted(MailTemplateCategory $category)
    {
        //
    }
}
