<?php

namespace Modules\TicketCalendar\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\TicketCalendar\Entities\Event;
use App\Models\Ticket;
use Modules\TicketCalendar\Jobs\EventReminderJob;
use Modules\TicketCalendar\Jobs\TicketDeadLineReminderJob;
use App\Notifications\Admin\Ticket\TicketEventNotification;
class EventReminder extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'event:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command will remind users with ticket events.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       $events = Event::where('date_time',date('Y-m-d'))->with('ticket.assigns')->get();
       $events_with_tickets_with_assigns = $events->map(function($event){
           return [
                'title' => $event->title,
                'description' => $event->description,
                'date'        => $event->date_time,
                'ticket'      => [
                    'id' => $event->ticket->id,
                    'title' => $event->ticket->name,
                    'assigns' => $event->ticket->assigns->map(function($assign){
                            return [
                                'id'    => $assign->id,
                                'name'  => $assign->name,
                                'email' => $assign->email
                            ];
                     })
                ],

           ];
       });


       foreach ($events_with_tickets_with_assigns as $key => $event) {
           EventReminderJob::dispatch($event);
           \Notification::send(getRoleUsers('Full Administrator'),new TicketEventNotification($event));
       }

       $tickets = Ticket::where('due_date','like',date('Y-m-d')."%")->with('assigns')->get();
       $ticke_with_assigns = $tickets->map(function($ticket){
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->name,
                    'date'  => $ticket->due_date,
                    'assigns' => $ticket->assigns->map(function($assign){
                            return [
                                'id'    => $assign->id,
                                'name'  => $assign->name,
                                'email' => $assign->email
                            ];
                     })
                    ];
       });
       foreach ($ticke_with_assigns as $key => $ticket) {
           TicketDeadLineReminderJob::dispatch($ticket);
       }
    }
}
