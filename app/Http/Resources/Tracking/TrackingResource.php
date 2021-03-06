<?php

namespace App\Http\Resources\Tracking;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\Task\TaskResource;
use Carbon\Carbon;
class TrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "comment" => $this->comment,
            "start_at" => Carbon::parse($this->start_at)->format('d-m-Y H:i:s'),
            "end_at" => Carbon::parse($this->end_at)->format('d-m-Y H:i:s'),
            "count_time" => $this->count_time,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => new UserResource($this->whenLoaded('creator')),
            "updated_by" => new UserResource($this->whenLoaded('updater')),
            "task" => new TaskResource($this->whenLoaded('task')),
        ];
    }
}
