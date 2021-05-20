<?php

namespace App\Jobs\Ticket;

use App\Notifications\Ticket\ReplyTicketNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\TicketComment\Entities\TicketComment;

class ReplyTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ticketComment;
    public $user;
    public $description;
    public $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TicketComment $ticketComment, $user, $description, $subject)
    {
        $this->ticketComment = $ticketComment;
        $this->user = $user;
        $this->description = $description;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send('emails.email-not-sent', ['ticketComment' => $this->ticketComment], function($message) {

        });

        $temp = \App::getLocale();
        \App::setLocale(isset($this->ticketComment->ticket->owner->first()->metadata->language) ? $this->ticketComment->ticket->owner->first()->metadata->language : 'de');
        try {
            // $notify = $this->ticketComment->ticket->owner->notify(new ReplyTicketNotification($this->ticketComment, $this->user, $this->description, $this->subject));


            if($this->ticketComment->ticket->owner->count() > 0) {
                foreach($this->ticketComment->ticket->owner as $owner){
                    if($owner->support === 0) {
                        $owner->notify(new ReplyTicketNotification($this->ticketComment, $owner, $this->description, $this->subject));
                    }
                }
            }



            // notify all assigns with reply
            if($this->ticketComment->ticket->assigns->count() > 0) {
                foreach($this->ticketComment->ticket->assigns as $assign){
                    if($assign->support === 0) {
                        $assign->notify(new ReplyTicketNotification($this->ticketComment, $assign, $this->description, $this->subject));
                    }
                }
            }


        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }

        \App::setLocale($temp);
    }



    public function failed()
    {
        \Mail::send('emails.email-not-sent', ['ticketComment' => $this->ticketComment], function($message) {
            $message->to($this->ticketComment->creator->email)->subject
               ($this->ticketComment->ticket->name." replay not sent");
            $message->from('support@tecsee.de','Alferp');
            $message->setContentType('text/html');
         });

    }


}
