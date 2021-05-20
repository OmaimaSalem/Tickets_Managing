<?php

namespace Modules\MailTemplate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class MailTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subject;
    private $body;
    private $mails;
    private $attachments;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->subject = $request->subject;
        $this->body = $request->body;
        $this->mails = $request->touser;
        $this->attachments = $request->attachments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      if(strpos($this->mails, ",")) {
        $mymails = explode(',', $this->mails);
        $mails = array();
        foreach($mymails as $mail) {
          array_push($mails, $mail);
        }
        $emails = $mails;
      } else {
        $emails = $mails;
      }
      try {
        $files = $this->attachments;
        Mail::send('emails.reply', ['body' => $this->body], function($message) use ($emails) {
             $message->to($emails)->subject($this->subject);
             if(count($this->attachments) > 1) {
               foreach ($this->attachments as $attachment) {
                 if(Storage::disk('local')->exists('public/templates_files/' . $this->attachments)) {
                   $file = Storage::disk('local')->path('public/templates_files/' . $this->attachments);
                 }
                 $url = Storage::url('public/templates_files/'. $attachment);
                 $message->attach(storage_path('app/public/templates_files/'. $attachment), ['as' => $attachment, 'mime' => 'application/pdf']);
               }
             } elseif(count($this->attachments) == 1) {
               $url = Storage::url('public/templates_files/'. $this->attachments);
               $message->attach(storage_path('app/public/templates_files/' . $this->attachments), ['as' => $this->attachments, 'mime' => 'application/pdf']);
             }
         });

      } catch (\Exception $ex) {
          throw new \Exception($ex);
      }
    }
}
