<?php

namespace Modules\MailTemplate\Observers;


use Modules\Activity\Http\Controllers\ActivityController;
use Modules\MailTemplate\Entities\MailTemplate;



class MailTemplateObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(MailTemplate $template)
    {

    }
    /**
     * Handle the MailTemplate "created" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplate  $ticketComment
     * @return void
     */
    public function created(MailTemplate $template)
    {
        //
    }

    /**
     * Handle the MailTemplate "updated" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplate  $ticketComment
     * @return void
     */
    public function updated(MailTemplate $template)
    {
        //
    }

    /**
     * Handle the MailTemplate "deleted" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplate  $ticketComment
     * @return void
     */
    public function deleted(MailTemplate $template)
    {
        //
    }

    /**
     * Handle the MailTemplate "restored" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplate  $ticketComment
     * @return void
     */
    public function restored(MailTemplate $template)
    {
        //
    }

    /**
     * Handle the MailTemplate "force deleted" event.
     *
     * @param  Modules\MailTemplate\Entities\MailTemplate  $ticketComment
     * @return void
     */
    public function forceDeleted(MailTemplate $template)
    {
        //
    }
}
