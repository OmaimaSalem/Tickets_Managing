<?php

namespace Modules\TicketDialog\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;
use Modules\TicketDialog\Entities\TicketSubReplyCC;

class ReplyResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
     public function checkSubReplies($check){
         if($check != null){
             return  SubReplyResource::collection($check);
         }else{
             return [];
         }
     }

     public function checkSubRepliesCC($check){
         if($check != null){
             return  TicketSubReplyCC::weher('id', '=', $check)->get();
         }else{
             return [];
         }
     }

    public function toArray($request)
    {
        $created_by = User::find($this->created_by);
        
        return [

            'id' => $this->id,
            'content' => $this->content,
            'subject' => $this->subject,
            'subRepliesNum' => $this->subRepliesNum,
            'subReplies' => $this->checkSubReplies($this->subReplies->reverse()),
            'created_by' => $created_by ? $created_by->only(['name', 'id','email']) : [],
            'created_at' => $this->created_at,
            'cc' => $this->subReplyCC,
        ];
    }
}
