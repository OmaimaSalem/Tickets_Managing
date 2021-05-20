<?php

namespace Modules\TaskComment\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TaskCommentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $id, $comment, $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id,$comment,$type)
    {
        $this->id     = $id;
        $this->comment = $comment;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['task-comment-added-'.$this->id];
        //  return new Channel('task-comment-added-'.$this->id);
    }

    public function broadcastAs()
    {
        return 'task.comment.created';
    }

}
