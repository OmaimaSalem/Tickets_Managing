<?php

namespace App\Http\Resources\Status;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserResource;

class StatusResource extends JsonResource
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
            "id" =>           $this->id,
            "name" =>         $this->name,
            // "description" =>  $this->when(!request()->index, $this->description),
            // "main" =>         $this->when(!request()->index, $this->main),
            // "created_at" =>   $this->when(!request()->index, $this->created_at),
            // "updated_at" =>   $this->when(!request()->index, $this->updated_at),
            // "created_by" =>   $this->when(!request()->index, new UserResource($this->whenLoaded('creator'))),
            // "updated_by" =>   $this->when(!request()->index, new UserResource($this->whenLoaded('updater'))),
        ];
    }
}
