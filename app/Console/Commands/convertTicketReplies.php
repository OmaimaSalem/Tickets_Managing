<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\TicketComment\Entities\TicketComment;
use Modules\TicketConversation\Entities\TicketConversation;
use Modules\TicketDialog\Entities\Comment;
use Modules\TicketDialog\Entities\Reply;

class convertTicketReplies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ticket:convertTicketReplies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Ticket Conversations to new table Ticket Replies';

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
        $conversations = TicketConversation::all();
        foreach($conversations as $conversation) {
            $reply = new Reply();
            $reply->ticket_id = $conversation->ticket_id;
            $reply->content = $conversation->conversation;
            $reply->created_by = $conversation->created_by;
            $reply->created_at = $conversation->created_at;
            $reply->updated_at = $conversation->updated_at;
            $reply->save();
            echo "conversation id: " . $conversation->id . " saved successfuly\n";
        }
        $comments = TicketComment::all();
        foreach($comments as $comment) {
            $newComment = new Comment();
            $newComment->ticket_id = $comment->ticket_id;
            $newComment->subject = '';
            $newComment->content = $comment->comment;
            $newComment->created_at = $comment->created_at;
            $newComment->updated_at = $comment->updated_at;
            $newComment->created_by = $comment->created_by;
            $newComment->save();
            echo "comment id: " . $comment->id . " saved successfuly\n";
        }
        echo "All old replies and comments transfared successfuly\n";
    }
}
