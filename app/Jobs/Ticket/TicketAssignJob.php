<?php

namespace App\Jobs\Ticket;

use App\Exceptions\ItemNotFoundException;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\Ticket\AssignTicketNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class TicketAssignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $users, $ticket;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users, Ticket $ticket)
    {
        $this->users = $users;
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = [];
        $emails = [];
        foreach($this->users as $user) {
            array_push($users, User::find($user));
        }
        
        $temp = \App::getLocale();

            foreach ($users as $user) {
                if($user->support === 0) {
                    // dd($user);
                    if(isset($user->metadata)) {
                        \App::setLocale(isset($user->metadata->language) ? $user->metadata->language : 'de');
                    } else {
                        \App::setLocale('de');
                    }
                    // array_push($emails, $user->email);
                    $user->notify(new AssignTicketNotification($this->ticket));
                }
            }
        

        // try {
        //     Mail::send('emails.ticket_assign', ['ticket' => $this->ticket], function($message) use ($emails) {
        //         $message->to($emails)->subject(__('Mail/Ticket/TicketAssign.ticketAssign'))
        //         ->bcc(config('mail.bcc'));
        //     });        
        // } catch (\Exception $ex) {
        //     throw new \Exception($ex);
        // }

        \App::setLocale($temp);
    }
}
