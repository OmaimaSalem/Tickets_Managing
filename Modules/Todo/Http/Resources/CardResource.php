<?php

namespace Modules\Todo\Http\Resources;
use App\Http\Resources\User\UserResource;
use App\Models\User;

use Modules\Todo\Http\Resources\TaskResource;
use Modules\Todo\Http\Resources\BoardResource;
use Illuminate\Http\Resources\Json\Resource;

class CardResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
          "id" => $this->id,
          "name" => $this->name,
          "created_at" => $this->created_at,
          "updated_at" => $this->updated_at,
          //"board_id" => $this->board_id,


          //"board" => new BoardResource($this->whenLoaded("board")),
          "tasks" =>  TaskResource::collection($this->whenLoaded("tasks")),
          // "users" =>  UserResource::collection(User::all()),
        ];
    }
}
