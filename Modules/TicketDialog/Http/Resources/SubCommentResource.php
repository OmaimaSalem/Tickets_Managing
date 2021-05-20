<?php

namespace Modules\TicketDialog\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;

class SubCommentResource extends Resource
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

            'id' => $this->id,
            'content' => $this->content,
            'subject' => $this->subject,
            'comment_id' => $this->comment_id,
            'created_by' => User::find($this->created_by)->only(['name', 'id','email']),
            'created_at' => $this->created_at
        ];
    }
}
