<?php

namespace App\Http\Resources\Project\ProjectDiscussion;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDiscussionRepliesResource extends JsonResource
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
            "title" => $this->title,
            "content" => $this->content,
            "created_by" => User::find($this->created_by)->only(['name','id']),
            "discussion_id" => $this->discussion_id,
            "created_at" => $this->created_at,
            'reply' => $this->reply,
        ];
    }
}
