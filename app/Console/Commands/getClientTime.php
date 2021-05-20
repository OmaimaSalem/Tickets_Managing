<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientTime;
use Carbon\Carbon;
use App\Jobs\Client\SendTimeExceededNotification;
use App\Jobs\Client\SendTimeReport;

class getClientTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command to calculate client time';
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
        // $times = ClientTime::with(['user.client_tickets.tracking','user.client_tasks'])->get();
        $times = ClientTime::with(['user.client_tickets'])->get();

        $tickets = '';
        foreach($times as $time) {

            if($time->message_sent) continue;



            // get tasks
            $tasks = $time->user->client_tasks->load(['tracking_history' => function($track)  use($time)  {
                $track->where('start_at','>=',$time->last_run_time)->where('end_at','<=',$time->next_run_time);
            }]);
            // get tickets
            $tickets = $time->user->client_tickets->load(['tracking' => function($track)  use($time)  {
                $track->where('start_at','>=',$time->last_run_time)->where('end_at','<=',$time->next_run_time);
            }]);
            $alltime = 0;
            foreach ($tickets as $ticket) {

                $alltime += $ticket->tracking->sum('count_time');
            }
            foreach ($tasks as $task) {
                $alltime += $task->tracking_history->sum('count_time');
            }








            $alltimeinhours = $alltime / 3600;


            if($alltimeinhours > $time->time) {
                if(!$time->message_sent) {
                  SendTimeReport::dispatch(['all_tasks' => $tasks,'all_tickets' => $tickets, 'alltime' => $alltimeinhours,'exceed' => true,'time' => $time]);
                }
            }




            if($time->next_run_time ==  Carbon::now()->format('Y-m-d'))
            {
                $time->last_run_time = Carbon::now()->format('Y-m-d');
                $time->next_run_time = $this->next_run_time(Carbon::now()->format('Y-m-d'),$time->frequency);
                $time->message_sent  = 0;
                $time->save();
                // send report
                $exceed = false;
                if($alltimeinhours > $time->time) {
                  $exceed = true;
                }

                SendTimeReport::dispatch(['tasks' => $tasks,'tickets' => $tickets, 'alltime' => $alltimeinhours,'exceed' => $exceed]);
            }

        }


    }




    public function next_run_time($time,$frequency) {
        $next_time = "";
        switch($frequency){
            case "weekly":
                $next_time = Carbon::parse($time)->addWeeks(1);
            break;
            case "monthly":
                $next_time = Carbon::parse($time)->addMonths(1);
            break;

            case "yearly":
                $next_time = Carbon::parse($time)->addYears(1);
            break;

            default:
                $next_time = Carbon::parse($time)->addMonths(1);
            break;
        }
        return $next_time;
    }


}
