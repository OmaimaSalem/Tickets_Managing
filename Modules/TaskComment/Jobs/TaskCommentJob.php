<?php

namespace Modules\TaskComment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class TaskCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $comment;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        Mail::send('emails.taskComment', ['comment' => $this->comment], function($message){
            $message->to($this->comment->task->responsible->email)->subject('New comment from '.$this->comment->creator->name.' on '.$this->comment->task->name.' task');
        });

    }
}
