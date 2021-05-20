<?php

namespace Modules\MailTemplate\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TemplateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
      return [
          'data' => TemplateResource::collection($this->collection),
      ];
    }
}
