<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Notifications\Attendees\AbsenceNotification;

class AttendeesCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendees:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command to notify user if he didn\'t add his attendees';

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

        $users = [];
        $absences = User::where('type','regular-user')->whereDoesntHave('attendes',function($q){
            $q->where('date',date('Y-m-d'));
        })->whereDoesntHave("vacations",function($q){
            $q->where('day_from',"<=",date('Y-m-d'));
            $q->where('day_to',">=",date('Y-m-d'));
        })->get(['id','name','email']);

        $absences_ids = $absences->pluck('id');
        $users['absence'] = $absences;


        $unclosedtime = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){
            $q->whereNull('to')->where('date',date('Y-m-d'));
            $q->orWhereNull('day_time')->where('date',date('Y-m-d'));
        })->with(['attendes' => function($q){
            $q->where('date',date('Y-m-d'));
        }])->get(['id','name','email']);
        $users['unclosedtime'] = $unclosedtime;


        // get no break time
        $nobreaks = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){
            $q->where('date',date('Y-m-d'));
            $q->whereDoesntHave('breaks');
        })->get(['id','name','email']);
        $users['nobreaks'] = $nobreaks;


        // get un closed break time
        $unclosedbreak = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){
            $q->where('date',date('Y-m-d'));
            $q->wherehas('breaks',function($q){
                $q->whereNull('to')->orwhereNull('break_time');
            });
        })->get(['id','name','email']);
        $users['unclosedbreak'] = $unclosedbreak;

        foreach ($users as $type => $usersCollection) {
            foreach ($usersCollection as $user) {
                $user->notify(new AbsenceNotification($type));
            }
        }



    }
}
