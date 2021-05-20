<?php

namespace Modules\TicketDialog\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class TicketReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $ticketReply,$oldReplies, $files, $MailsArr, $ticket,$Cc,$Bcc,$sender_email;
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ticketReply,$oldReplies, $files, $MailsArr, $ticket,$Cc,$Bcc,$sender_email)
    {
        $this->ticketReply = $ticketReply;
        $this->oldReplies = $oldReplies;
        $this->files = $files;
        $this->MailsArr = $MailsArr;
        $this->ticket = $ticket;
        $this->Cc = $Cc;
        $this->Bcc = $Bcc;
        $this->sender_email = $sender_email;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ticketReply= $this->ticketReply;
        $oldReplies= $this->oldReplies;
        $files= $this->files;
        $MailsArr= $this->MailsArr;
        $Cc_arr = $this->Cc;
        $Bcc_arr = $this->Bcc;
    Mail::send('emails.ticketReplyMail', ['body' => $ticketReply->content /*, 'oldReplies' => $oldReplies*/], function($message) use ($MailsArr, $ticketReply, $files, $Cc_arr,$Bcc_arr) {
            if(isset($ticketReply->reply_id)) {
            if(isset($Bcc_arr) && count($Bcc_arr) > 0 && isset($Cc_arr) && count($Cc_arr) > 0) {
                  $message->to($MailsArr)->cc($Cc_arr)->bcc($Bcc_arr)->subject('##'.$this->ticket->number."##" .$ticketReply->reply_id.'-' . $this->ticketReply->subject);
              } elseif(isset($Cc_arr) && count($Cc_arr) > 0) {
                    $message->to($MailsArr)->cc($Cc_arr)->subject('##'.$this->ticket->number."##" .$ticketReply->reply_id.'-' . $this->ticketReply->subject);
                } elseif(isset($Bcc_arr) && count($Bcc_arr) > 0) {
                    $message->to($MailsArr)->bcc($Bcc_arr)->subject('##'.$this->ticket->number."##" .$ticketReply->reply_id.'-' . $this->ticketReply->subject);
                }  else {
                    $message->to($MailsArr)->subject('##'.$this->ticket->number."##" .$ticketReply->reply_id.'-' . $this->ticketReply->subject);
                }
            } else {
                if(isset($Bcc_arr) && count($Bcc_arr) > 0 && isset($Cc_arr) && count($Cc_arr) > 0) {
                    $message->to($MailsArr)->cc($Cc_arr)->bcc($Bcc_arr)->subject('##'.$this->ticket->number."##". $this->ticketReply->subject);
                }
                elseif(isset($Cc_arr) && count($Cc_arr) > 0) {
                    $message->to($MailsArr)->cc($Cc_arr)->subject('##'.$this->ticket->number."##". $this->ticketReply->subject);
                } elseif(isset($Bcc_arr) && count($Bcc_arr) > 0) {
                    $message->to($MailsArr)->bcc($Bcc_arr)->subject('##'.$this->ticket->number."##". $this->ticketReply->subject);
                }  else {
                    $message->to($MailsArr)->subject('##'.$this->ticket->number."##". $this->ticketReply->subject);
                }
            }

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



    public function failed(\Throwable $exception)
    {
        Mail::raw("the replay to ticket ## {$this->ticket->number} ## {$ticketReply->reply_id} - {$this->ticketReply->subject} failed", function ($message) {
            $message->to($this->sender_email);
        });
    }

}
