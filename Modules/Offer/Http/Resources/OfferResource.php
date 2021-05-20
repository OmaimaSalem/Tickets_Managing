<?php

namespace Modules\Offer\Http\Resources;

use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OfferItems\Http\Resources\OfferItemCollection;
use Modules\OfferItems\Http\Resources\OfferItemResource;
use Modules\Contract\Http\Resources\ContractResource;

class OfferResource extends JsonResource
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
            "number" => $this->number,
            "total" => $this->total,
            "date" => $this->date,
            "greeting" => $this->when(!$request->index,$this->greeting),
            "street" => $this->when(!$request->index,$this->street),
            "country" => $this->when(!$request->index,$this->country),
            "city" => $this->when(!$request->index,$this->city),
            "first_name" => $this->first_name,
            "postal_code" => $this->when(!$request->index,$this->postal_code),
            "telephone" => $this->when(!$request->index,$this->telephone),
            "mobile" => $this->when(!$request->index,$this->mobile),
            "title" => $this->when(!$request->index,$this->title),
            "name" => $this->when(!$request->index,$this->name),
            "fax" => $this->when(!$request->index,$this->fax),
            "email" => $this->when(!$request->index,$this->email),
            "others" => $this->when(!$request->index,$this->others),
            "notes" => $this->when(!$request->index,$this->notes),
            "total_tax" => $this->when(!$request->index,$this->total_tax),
            "printed_text" => $this->when(!$request->index,$this->printed_text),
            "created_at" => Carbon::parse($this->created_at)->format('d-m-yy'),
            "updated_at" => $this->when(!$request->index,Carbon::parse($this->updated_at)->format('d-m-yy')),
            "items" => $this->when(!$request->index,new OfferItemCollection($this->whenLoaded("offer_items"))),
            "client" => new UserResource($this->whenLoaded("client")),
            "contract" => new ContractResource($this->whenLoaded('contract')),
            "created_by" => new UserResource($this->whenLoaded("creator")),
            "updated_by" => $this->when(!$request->index,new UserResource($this->whenLoaded("updater"))),
        ];
    }
}
