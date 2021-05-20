<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;
use Modules\TicketConversation\Entities\TicketConversation;

class ticketConversations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ticketConversations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pull ticket description to ticket conversation table';

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
        $tickets = Ticket::all();
        foreach($tickets as $ticket) {
            $conversation = new TicketConversation();
            $conversation->ticket_id = $ticket->id;
            $conversation->created_by = $ticket->created_by;
            $data = new \DOMDocument;
            @$data->loadHTML($ticket->description);
            $description = $data->saveHTML($data);
            $conversation->conversation = '<div class="card card-primary card-outline">
                                        <div class="card-body p-0">
                                            <div class="mailbox-read-message">
                                                <p>'. $description .'</p>
                                            </div>
                                        </div>
                                    </div>';
            $conversation->save();
        }
    }
}
