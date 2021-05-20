<?php

namespace Modules\OfferConversations\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\OfferConversations\Entities\OfferConversation;
use Modules\OfferConversations\Notifications\ReplyOfferConversationNotification;
use Illuminate\Support\Facades\Mail;

class ReplyOfferConversationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $offerConversation;
    public $user;
    public $description;
    public $subject;
    public $mails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public function __construct(OfferConversation $offerConversation, $user, $description, $subject, $mails)
     {
         $this->offerConversation = $offerConversation;
         $this->user = $user;
         $this->description = $description;
         $this->subject = $subject;
         $this->mails = $mails;
     }

     /**
      * Execute the job.
      *
      * @return void
      */
     public function handle()
     {
         $temp = \App::getLocale();

         \App::setLocale(isset($this->offerConversation->offer->client->metadata->language) ? $this->offerConversation->offer->client->metadata->language : 'de');

         try {
           $emails = $this->mails;
           Mail::send('emails.offer-reply', ['description' => $this->description, 'offerConversation' => $this->offerConversation], function($message) use ($emails) {
                $message->to($emails)->subject('##Offer-' . $this->offerConversation->offer->number . '## ' . $this->subject);
            });
             // $this->offerConversation->offer->client->notify(new ReplyOfferConversationNotification($this->offerConversation, $this->user, $this->description, $this->subject));
         } catch (\Exception $ex) {
             throw new \Exception($ex);
         }

         \App::setLocale($temp);
     }
}
