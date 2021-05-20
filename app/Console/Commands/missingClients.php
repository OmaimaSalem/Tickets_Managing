<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
class missingClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:missing-clients';

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
        $users = User::where("name","")->where("type","client");
        $users->each(function($user){
            $email_array = explode("@",$user->email);
            $user->name = $email_array[0];
            $user->save();
        });
    }
}
