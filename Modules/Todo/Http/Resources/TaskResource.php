<?php

namespace Modules\Todo\Http\Resources;

use Modules\Todo\Http\Resources\CardResource;
use Illuminate\Http\Resources\Json\Resource;

class TaskResource extends Resource
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
          "status" => $this->status,
          "created_at" => $this->created_at,
          "updated_at" => $this->updated_at,
          "card_id" => $this->card_id,
          "description" => $this->description,
          "card" => new CardResource($this->whenLoaded("card")),
          "assigned_to" => $this->users, //return assigned user
          "deadline" => $this->deadline
        ];
    }
}
