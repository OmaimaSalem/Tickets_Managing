<?php

namespace Modules\Activity\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Ticket\TicketResource;
use App\Http\Resources\Task\TaskResource;

class ActivityResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function returnUser($user){
        if($user == null){
           return null;
        }
        else{
            return $user->only(['name' , 'id']);
        }
    }


    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "subject" => $this->subject,
            // "user" => new UserResource($this->whenLoaded('user')),
            "user" => $this->returnUser($this->user),
            // "client" => new UserResource($this->whenLoaded('client')),
            // "project" => new ProjectResource($this->whenLoaded('project')),
            // "ticket" => new TicketResource($this->whenLoaded('ticket')),
            // "task" => new TaskResource($this->whenLoaded('task')),
            "created_at" => $this->created_at,
        ];
    }
}
