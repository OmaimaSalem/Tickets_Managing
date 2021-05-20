<?php

namespace Modules\ItemCategory\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Item\Http\Resources\ItemsCollection;

class ItemCategoryResource extends JsonResource
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
          "description" => $this->description,
          "created_at" => $this->created_at,
          "updated_at" => $this->updated_at,
          "items" => new ItemsCollection($this->whenLoaded('items')),
          "created_by" => new UserResource($this->whenLoaded('creator')),
          "updated_by" => new UserResource($this->whenLoaded('updater')),
      ];
    }
}
