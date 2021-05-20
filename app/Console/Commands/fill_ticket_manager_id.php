<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class fill_ticket_manager_id extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ticket:Fill_manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill ticket manager_id if equal to 0';

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
        $mytickets = Ticket::whereDoesntHave('manager')->get();

        foreach ($mytickets as $ticket) {
          $ticket->manager()->attach(1);
        }
    }
}
