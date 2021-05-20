<?php

namespace Modules\TicketDialog\Entities;

use Illuminate\Database\Eloquent\Model;

class SubReply extends Model
{
    protected $table = 'ticket_sub_replies';
    protected $fillable = ['reply_id', 'created_by', 'content', 'subject'];

    public function ParentReply(){
    	return $this->hasOne('Modules\TicketDialog\Entities\Reply');
    }

    public function creator(){
    	return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function subReplyCC() {
      return $this->hasMany('Modules\TicketDialog\Entities\TicketSubReplyCC', 'sub_reply', 'id');
    }
}
