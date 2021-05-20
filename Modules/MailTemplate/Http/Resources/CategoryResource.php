<?php

namespace Modules\MailTemplate\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Modules\MailTemplate\Http\Resources\TemplateCollection;
use App\Http\Resources\User\UserResource;

class CategoryResource extends Resource
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
          "templates" => new TemplateCollection($this->whenLoaded('templates')),
          "created_at" => $this->created_at,
          "updated_at" => $this->updated_at,
          "created_by" => new UserResource($this->whenLoaded("creator")),
          "updated_by" => new UserResource($this->whenLoaded("updater")),
      ];
    }
}
