<?php

namespace App\Jobs\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ProjectDiscussionsRepliesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $projectDiscussionReply,$oldReplies, $files, $MailsArr;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($projectDiscussionReply,$oldReplies, $files, $MailsArr)
    {
        $this->projectDiscussionReply = $projectDiscussionReply;
        $this->oldReplies = $oldReplies;
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
         $projectDiscussionReply= $this->projectDiscussionReply;
         $oldReplies= $this->oldReplies;
         $files= $this->files;
         $MailsArr= $this->MailsArr;

        Mail::send('emails.discussionMail', ['body' => $projectDiscussionReply->content , 'oldReplies' => $oldReplies], function($message) use ($MailsArr, $projectDiscussionReply, $files) {

            $message->to($MailsArr)->subject($projectDiscussionReply->title);
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
