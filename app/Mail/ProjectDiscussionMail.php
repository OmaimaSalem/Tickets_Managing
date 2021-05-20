<?php

namespace App\Mail;

use App\Http\Resources\User\UserResource;
use App\Models\ProjectDiscussion;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectDiscussionMail extends Mailable
{
    use Queueable, SerializesModels;
    private $projectDiscussion;
    private $data;

    /**
     * Create a new message instance.
     *
     * @param ProjectDiscussion $projectDiscussion
     */
    public function __construct($projectDiscussion)
    {
        $this->projectDiscussion = $projectDiscussion;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Project Discussion for project number ')->view('emails.projectDiscussionMail')->with([
            'title' => $this->projectDiscussion->title,
            'content' => $this->projectDiscussion->content,
            'created_by' => User::find($this->projectDiscussion->created_by)->only(['name','id']),
        ]);

    }
}
