<?php

namespace Modules\ContractItems\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractItemResource extends JsonResource
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
            "price" => $this->price,
            "tax" => $this->tax,
            "tax_price" => $this->tax_price,
            "qty" => $this->qty,
            "total_price" => $this->total_price,
            "total" => $this->total,
            "category" => $this->category,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => new UserResource($this->whenLoaded("creator")),
            "updated_by" => new UserResource($this->whenLoaded("updater")),
        ];
    }
}
