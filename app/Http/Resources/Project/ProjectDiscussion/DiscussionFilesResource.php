<?php

namespace App\Http\Resources\Project\ProjectDiscussion;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionFilesResource extends JsonResource
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
          'id' => $this->id,
          'discussion_id' => $this->discussion_id,
            'file_path' => $this->file_path
        ];
    }
}
