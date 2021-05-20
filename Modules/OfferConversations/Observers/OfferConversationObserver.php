<?php

namespace Modules\OfferConversations\Observers;

use App\Jobs\Ticket\ReplyTicketJob;
use App\Jobs\Ticket\UpdateReplyTicketJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Modules\Activity\Http\Controllers\ActivityController;
use Modules\OfferConversations\Entities\OfferConversation;
use Modules\OfferConversations\Jobs\ReplyOfferConversationJob;


class OfferConversationObserver
{
    private $activityLog;

    public function __construct(ActivityController $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    public function retrieved(OfferConversation $offerConversation)
    {
        $offerConversation->offer;
        $offerConversation->creator;
    }

    public function creating(OfferConversation $offerConversation)
    {
        $mails = Request::input('to');
        // dd('fdsaasdfas');

    }

    public function created(OfferConversation $offerConversation)
    {
      if(Request::input('to')) {
        $description = Request::input('offer_mail');
        // $unsortConversations = $offerConversation->conversations;
        // $sortConversations = $unsortConversations->sortByDesc('created_at');
        // foreach($sortConversations as $conversation) {
        //     $description .= $conversation->conversation . '<hr />';
        // }
        $subject = Request::input('subject');
        $request_mails = Request::input('to');
        $mails = array();
        foreach($request_mails as $mail) {
          array_push($mails, $mail['email']);
        }
        $user = Auth::user();
        $offerConversation->creator;
        $offerConversation->updater;

        ReplyOfferConversationJob::dispatch($offerConversation, $user, $description, $subject, $mails);
        $this->activityLog->addToLog('Sending an email for replying on Offer: '.$offerConversation->offer->number, null, null, null, null, null,$offerConversation->offer->id);
      }
    }

    public function updated(OfferConversation $offerConversation)
    {
      //
    }

    public function deleted(OfferConversation $offerConversation)
    {
      //
    }

    public function restored(OfferConversation $offerConversation)
    {
      //
    }

    public function forceDeleted(OfferConversation $offerConversation)
    {
      //
    }
}
