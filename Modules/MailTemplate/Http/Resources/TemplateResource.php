<?php

namespace Modules\MailTemplate\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Http\Resources\User\UserResource;

class TemplateResource extends Resource
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
          "title" => $this->title,
          "subject" => $this->subject,
          "body" => $this->body,
          "files" => AttachmentResource::collection($this->files),
          "created_at" => $this->created_at,
          "updated_at" => $this->updated_at,
          "mail_template_category_id"=> $this->mail_template_category_id,
          "created_by" => new UserResource($this->whenLoaded("creator")),
          "updated_by" => new UserResource($this->whenLoaded("updater")),
      ];
    }
}
