<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\TicketCalendar\Console\EventReminder;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ticketConversations::class,
        Commands\setTasksStatus::class,
        Commands\setTicketsOwner::class,
        Commands\fill_ticket_manager_id::class,
        Commands\fill_ticket_manager_id::class,
        Commands\sendAttributesEmails::class,
        Commands\AutomaticBreaks::class,
        Commands\repeatTasks::class,
        EventReminder::class
        //App\Console\Commands\setTasksStatus

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('minute:refreshMailBoxToCreateTickets')
                 ->everyMinute();

        // $schedule->command('attendees:report')
        //          ->dailyAt('20:00');



        $schedule->command('attributes:sendEmails')
                  ->everyMinute();


        //set daily tasks status to zero every day
         $schedule->command('task:update')->dailyAt('7:00');
         $schedule->command('repeat:tasks')->dailyAt('4:00');
         $schedule->command('event:reminder')->dailyAt('7:00');
         $schedule->command('projects:check-due_dates')->dailyAt('7:00');
         $schedule->command('employee:automaticbreaks')->dailyAt('19:00');



    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
