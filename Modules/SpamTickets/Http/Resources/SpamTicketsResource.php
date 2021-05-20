<?php

namespace Modules\SpamTickets\Http\Resources;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;

class SpamTicketsResource extends Resource
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
            "description" =>  $this->description,
            "read" => $this->read,
            "created_at" => $this->created_at,
            "created_by" => new UserResource(User::find($this->created_by)),
            "owner" => new UserResource(User::find($this->owner_id)),
        ];
    }
}
