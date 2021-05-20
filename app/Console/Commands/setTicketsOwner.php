<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class setTicketsOwner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ticket:setOwner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set owner id for all tickets';

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

        $tickets = Ticket::where('project_id','!=',Null)->get();

        foreach($tickets as $ticket){

            $project_owner_id = Project::find($ticket->project_id)->owner_id;

            $ticket->update(['owner_id' => $project_owner_id]);
        }

    }
}
