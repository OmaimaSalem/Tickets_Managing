<?php

namespace Modules\TicketDialog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class TicketCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $ticketComment, $MailsArr, $files, $ticket;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ticketComment, $MailsArr, $files, $ticket)
    {
        $this->ticketComment = $ticketComment;
        $this->MailsArr = $MailsArr;
        $this->files = $files;
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $MailsArr = $this->MailsArr;
        $files = $this->files;
        Mail::send('emails.ticketcommentReply', ['body' => $this->ticketComment->content , 'ticketComment' => $this->ticketComment], function($message) use ($MailsArr) {
            $message->to($MailsArr)
            ->subject(__('Mail/Ticket/CommentTicketNotification.subject'))
            ->bcc(config('mail.bcc'));

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
