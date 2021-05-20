<?php

namespace Modules\MailTemplate\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\User\UserResource;

class AttachmentResource extends Resource
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
          "attachment_path" => $this->attachment_path,
          "created_by" => new UserResource($this->whenLoaded('creator')),
          "updated_by" => new UserResource($this->whenLoaded('updater')),
      ];
    }
}
