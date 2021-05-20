<?php

namespace App\Jobs\Attendees;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\Attendees\AttendReviewMail;
use Illuminate\Support\Facades\Mail;

class AttendeesReviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $attend;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attend)
    {
       $this->attend = $attend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to('hr@tecsee.de')->send(new AttendReviewMail($this->attend));
    }
}
