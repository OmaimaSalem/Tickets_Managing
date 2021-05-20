<?php

namespace Modules\TicketDialog\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;
use Modules\TicketDialog\Entities\TicketSubReplyCC;
use Modules\TicketDialog\Http\Resources\ReplyCCResouce;

class SubReplyResource extends Resource
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
            'subject' => $this->subject,
            'content' => $this->content,
            'reply_id' => $this->reply_id,
            'created_by' => User::find($this->created_by)->only(['name', 'id','email']),
            'created_at' => $this->created_at,
        ];
    }
}
