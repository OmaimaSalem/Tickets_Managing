<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Setting\SettingResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Task\TaskResource;
use App\Http\Resources\Ticket_file\Ticket_fileResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\TicketConversation\Http\Resources\TicketConversationResource;

class LightTicketResource extends JsonResource
{
    
    public function returnOwner($owners){
        if($owners && $owners->count() > 0){
            return $owners->map(function($owner){
                return new UserResource(User::find($owner->id));
            });
        }
        return [];
    }

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" =>  $this->when(!request()->index, $this->description),
            "number" => $this->number,
            "owner" => $this->returnOwner($this->owner),
        ];
    }
}
