<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AutomaticBreaks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:AutomaticBreaks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $absences = User::where('type','regular-user')->whereDoesntHave('attendes',function($q){
            $q->where('date',date('Y-m-d'));
        })->whereDoesntHave("vacations",function($q){
            $q->where('day_from',"<=",date('Y-m-d'));
            $q->where('day_to',">=",date('Y-m-d'));
        })->get(['id','name','email']);

        $absences_ids = $absences->pluck('id');


        $nobreaks_users = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){
            $q->where('date',date('Y-m-d'));
            $q->whereDoesntHave('breaks');
        })->with(['attendes' => function($attend){
            $attend->where('date',date('Y-m-d'));
        }])->get(['id','name','email']);

        foreach ($nobreaks_users as  $user) {
            $attend = $user->attendes->first();
            $time_in_hours = round($attend->day_time / 60);
            $break_time    = 30;
            if($time_in_hours > 6) {
                $break_time    = 45;
            }elseif ($time_in_hours > 8){
                $break_time    = 60;
            }
            $time = strtotime('12:00:00');
            $attend->breaks()->create([
                'break_time' => $break_time,
                'from'       => date("H:i:s",$time),
                'to'         => date("H:i:s", strtotime('+'.$break_time.' minutes', $time))
            ]);
        }
    }
}
