<?php

namespace Modules\Contract\Http\Resources;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ContractItems\Http\Resources\ContractItemCollection;

class ContractResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        if($request->index){
            return [
                "id" => $this->id,
                "number" => $this->number,
                "client" => new UserResource($this->whenLoaded("client")),
                "created_at" => $this->created_at,
                "total" => $this->total,
            ];
        }
        return [
            "id" => $this->id,
            "number" => $this->number,
            "total" => $this->total,
            "date" => $this->date,
            "greeting" => $this->greeting,
            "street" => $this->street,
            "country" => $this->country,
            "city" => $this->city,
            "first_name" => $this->first_name,
            "postal_code" => $this->postal_code,
            "telephone" => $this->telephone,
            "mobile" => $this->mobile,
            "title" => $this->title,
            "name" => $this->name,
            "fax" => $this->fax,
            "email" => $this->email,
            "others" => $this->others,
            "notes" => $this->notes,
            "printed_text" => $this->printed_text,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "items" => new ContractItemCollection($this->whenLoaded("contract_items")),
            "client" => new UserResource($this->whenLoaded("client")),
            "created_by" => new UserResource($this->whenLoaded("creator")),
            "updated_by" => new UserResource($this->whenLoaded("updater")),
        ];
    }
}
