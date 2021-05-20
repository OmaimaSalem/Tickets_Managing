<?php

namespace Modules\OfferConversations\Http\Resources;

use Modules\Offer\Http\Resources\OfferResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferConversationResource extends JsonResource
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
          "conversation" => $this->conversation,
          "offer" => new OfferResource($this->whenLoaded('offer')),
          "created_at" => $this->created_at,
          "updated_at" => $this->updated_at,
          "created_by" => new UserResource($this->whenLoaded('creator')),
          "updated_by" => new UserResource($this->whenLoaded('updater')),
      ];
    }
}
