<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketAttributesResource extends JsonResource
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
            "attribute_id" => $this->attribute ? $this->attribute->id : null,
            "user_type" => $this->attribute ? $this->attribute->user_type : null,
            "type" => $this->attribute ? $this->attribute->type : null ,
            "name"   => $this->attribute ? $this->attribute->name : null,
            "value" => $this->value,
        ];
    }
}
