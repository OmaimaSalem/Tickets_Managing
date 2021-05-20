<?php

namespace Modules\TicketDialog\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;

class CommentResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function checkSubComments($check){
        if($check != null){
            return  SubCommentResource::collection($check);
        }else{
            return [];
        }
    }

    public function returnSubComments($subComments){
        if($subComments && $subComments->count() > 0){
            return $subComments->map(function($subComment){
                return $subComment->only(['id','comment_id','email']);
            });
        }
        return [];
    }
    public function toArray($request)
    {
        $created_by = User::find($this->created_by);

        return [
            'id' => $this->id,
            'content' => $this->content,
            'subject' => $this->subject,
            'subCommentsNum' => $this->subCommentsNum,
            'subComments' => $this->checkSubComments($this->subComments),
            'created_by' => $created_by ? $created_by->only(['name', 'id','email']) : [],
            'created_at' => $this->created_at
        ];
    }
}
