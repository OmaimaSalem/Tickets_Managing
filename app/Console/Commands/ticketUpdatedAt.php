<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class ticketUpdatedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ticketUpdatedAt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fill updated_at column in tickets table if it\'s null';

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
        $total = 0;
        foreach($tickets as $ticket) {
            if($ticket->updated_at == null) {
                $date = $ticket->created_at;
                $ticket->updated_at = $date;
                $ticket->created_at = $date;
                $ticket->save();
                $total += 1;
            }
        }
        echo "updated " . $total . " ticket(s)";
    }
}
