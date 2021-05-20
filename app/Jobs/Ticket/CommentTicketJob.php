<?php

namespace App\Jobs\Ticket;

use App\Models\User;
use App\Notifications\Ticket\CommentTicketNotification;
use App\Notifications\Ticket\ReplyTicketNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\TicketComment\Entities\TicketComment;

class CommentTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ticketComment;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TicketComment $ticketComment)
    {
        $this->ticketComment = $ticketComment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = [];
        $ticket = $this->ticketComment->ticket->manager;
        foreach($ticket as $user) {
            array_push($users, User::find($user->id));
        }


            // dd($users);
        foreach ($users as $user) {
            if($user->support === 0) {
                $temp = \App::getLocale();
            
                if(isset($user->metadata)) {
                    \App::setLocale(isset($user->metadata->language) ? $user->metadata->language : 'de');
                } else {
                    \App::setLocale('de');
                }
                // array_push($emails, $user->email);
                $user->notify(new CommentTicketNotification($this->ticketComment));
                
                \App::setLocale($temp);
            }
        }

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
