<?php

namespace App\Events\Admin;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($object_id,$notification,$id,$type)
    {
        $this->data['object_id']     = $object_id;
        $this->data['notification'] = $notification;
        $this->data['not_id'] = $id;
        $this->data['not_type'] = $type;
        if($id){
            $this->data['not_url'] = url('admin/'.$type."/".$id);
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user-'.$this->user->id);
    }

    public function broadcastAs()
    {
        return 'notification';
    }
}
