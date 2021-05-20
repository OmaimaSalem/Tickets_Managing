<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendeesReport as AttendeesReportTemplate;
class AttendeesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendees:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will notify the hr with employee reports';

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

            // get absence users
            $absences = User::where('type','regular-user')->whereDoesntHave('attendes',function($q){
                $q->where('date',date('Y-m-d'));
            })->get(['id','name']);

            $absences_ids = $absences->pluck('id');

            //get unclosedtime
            $unclosedtime = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){

                $q->whereNull('to')->where('date',date('Y-m-d'));
                $q->orWhereNull('day_time')->where('date',date('Y-m-d'));
            })->with(['attendes' => function($q){
                $q->where('date',date('Y-m-d'));
            }])->get(['id','name']);


            // get low work time
            $lowworktime = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){
                $q->where('day_time','<',480)->where('date',date('Y-m-d'));
                $q->orWhereNull('day_time')->where('date',date('Y-m-d'));
            })->with(['attendes' => function($q){
                $q->where('date',date('Y-m-d'));
            }])->get(['id','name']);
            // get high work time
            $highworktime = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){

                $q->where('day_time','>',600);
                $q->where('date',date('Y-m-d'));
            })->with(['attendes' => function($q){
                $q->where('date',date('Y-m-d'));
            }])->get(['id','name']);

            // get no break time
            $nobreaks = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){

                $q->where('date',date('Y-m-d'));
                $q->whereDoesntHave('breaks');
            })->get(['id','name']);

            // get un closed break time
            $unclosedbreak = User::where('type','regular-user')->wherenotin('id',$absences_ids)->whereHas('attendes',function($q){

                $q->where('date',date('Y-m-d'));
                $q->wherehas('breaks',function($q){
                    $q->whereNull('to')->orwhereNull('break_time');
                });
            })->get(['id','name']);



            $data = [
                'absences'      => ['title'=> 'Absences Users' ,'data' => $absences],
                'unclosedtime'  => ['title'=> 'unclosed time'   ,'data' => $unclosedtime],
                'lowworktime'   => ['title'=> 'low work time'    ,'data' => $lowworktime],
                'highworktime'  => ['title'=> 'high work time'   ,'data' => $highworktime],
                'nobreaks'      => ['title'=> 'no breaks'       ,'data' => $nobreaks],
                'unclosedbreak' => ['title'=> 'unclosed breaks'  ,'data' => $unclosedbreak],
            ];

            $email = env('HR_EMAIL');
            Mail::to('hr@tecsee.de')->send(new AttendeesReportTemplate($data));

    }
}
