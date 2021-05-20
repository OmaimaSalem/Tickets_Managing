<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttributeEmail;
use App\Models\User;
use Carbon\Carbon;
use App\Jobs\Attribute\sendAttributesEmailsJob;
class sendAttributesEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attributes:sendEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This comman used to send emails to users and clients';

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
        $attribute_emails = AttributeEmail::where('next_run_time','<=',date('Y-m-d H:i:s', time()))->get();
        foreach($attribute_emails as $attribute_email){

            if($attribute_email->user_type == "employee"){
                $users = User::where('type','regular-user')->whereHas('attributes',function($attribute) use($attribute_email){

                    $attribute->where('attribute_id',$attribute_email->attribute_id);

                    $values = str_replace('[','',$attribute_email->attribute_value);
                    $values = str_replace(']','',$values);
                    $values = explode(',',$values);
                    $attribute->where(function($q) use($values){
                        foreach ($values as $value) {
                            $q->orwhere('value','like','%'.$value.'%');
                        }
                    });
                })->get();

            }elseif($attribute_email->user_type == "client"){
                $users = User::where('type','client')->whereHas('attributes',function($attribute) use($attribute_email){

                    $attribute->where('attribute_id',$attribute_email->attribute_id);


                    $values = str_replace('[','',$attribute_email->attribute_value);
                    $values = str_replace(']','',$values);
                    $values = explode(',',$values);

                    $attribute->where(function($q) use($values){
                        foreach ($values as  $value) {
                            $q->orwhere('value','like','%'.$value.'%');
                        }
                    });

                })->get();

            }elseif($attribute_email->user_type == "allclients"){
                $users = User::where('type','client')->get();
            }else{
                $users = User::where('type','regular-user')->get();
            }
            foreach($users as $user) {
                sendAttributesEmailsJob::dispatch(['email_content' => $attribute_email->email_content,'user'=> $user]);
            }

            $this->next_run_date($attribute_email);
        }
    }



    public function next_run_date($attribute_email) {
        $carbon = Carbon::parse($attribute_email->next_run_time);
        switch($attribute_email->email_time) {
            case "daily":
                $attribute_email->next_run_time = $carbon->addDay();
            break;
            case "weekly":
                $attribute_email->next_run_time = $carbon->addWeek();
            break;
            case "last_day_in_the_month":
                $lastDateOfNextMonth = strtotime('last day of next month');
                $attribute_email->next_run_time =date('Y-m-d 00:00:00', $lastDateOfNextMonth);
            break;

            case "every_15_day":
                $attribute_email->next_run_time = $carbon->addMonthNoOverflow();
            break;

            case "every_year":
                $attribute_email->next_run_time = $carbon->addYear();
            break;
            case "every_three_months":
                $attribute_email->next_run_time = $carbon->addMonths(3);
            break;
            case "every_six_months":
                $attribute_email->next_run_time = $carbon->addMonths(6);
            break;

            case "specific_date":
                $attribute_email->next_run_time = $attribute_email->mail_start;

            break;
            default:
            $attribute_email->next_run_time = $attribute_email->mail_start;
        }
        $attribute_email->save();
    }

}
