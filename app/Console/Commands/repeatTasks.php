<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Todo\Entities\Task;
use Carbon\Carbon;

class repeatTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repeat:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'to repeat tasks';

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
        $tasks = Task::where('next_time_work','<=',date('Y-m-d H:i:s', time()))->get();
        foreach($tasks as $task){
            $task->last_time_work = date('Y-m-d H:i:s');
            $task->next_time_work = $this->next_run_date($task->freq);
            $task->status = 0;
            $task->save();
        }
    }



    public function next_run_date($type) {
        $carbon = Carbon::parse(date('Y-m-d H:i:s'));
        $date   = date('Y-m-d H:i:s');
        switch($type) {
            case "daily":
                 $date = $carbon->addDay();
            break;
            case "weekly":
                 $date = $carbon->addWeek();
            break;
  
            case "monthly":
                 $date = $carbon->addMonth();
            break;
  
            case "yearly":
                 $date = $carbon->addYear();
            break;
  
            break;
            default:
             $date = date('Y-m-d H:i:s');
        }
        return $date;
    }

    
}
