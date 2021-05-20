<?php

namespace App\Jobs\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ProjectDiscussionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $projectDiscussion,$files,$MailsArr;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($projectDiscussion, $files, $MailsArr)
    {
        $this->projectDiscussion = $projectDiscussion;
        $this->files = $files;
        $this->MailsArr = $MailsArr;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $MailsArr = $this->MailsArr;
        $projectDiscussion = $this->projectDiscussion ;
        $files = $this->files ;

        Mail::send('emails.discussionMail', ['body' => $this->projectDiscussion->content], function($message) use ($MailsArr, $projectDiscussion, $files) {

            $message->to($MailsArr)->subject($projectDiscussion->title);
            if (isset($files) && count($files) > 0) {
                if(count($files) > 1) {
                    foreach ($files as $file) {
                        $message->attach(storage_path($file['path']), ['as' => $file['name'], 'mime' => $file['type']]);
                    }
                } elseif(count($files) == 1) {
                    $file = $files[0];
                    $message->attach(storage_path($file['path']), ['as' => $file['name'], 'mime' => $file['type']]);
                }
            }
        });
    }
}
