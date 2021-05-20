<?php

namespace Modules\TicketDialog\Entities;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
	protected $table = 'ticket_replies';
    protected $fillable = ['ticket_id', 'created_by', 'content', 'subject'];

    public function ticket(){
        return $this->belongsTo('App\Models\Ticket');
    }

    public function subReplies(){
    	return $this->hasMany('Modules\TicketDialog\Entities\SubReply');
    }

    public function creator(){
    	return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
    public function subReplyCC() {
      return $this->hasMany('Modules\TicketDialog\Entities\TicketSubReplyCC', 'sub_reply', 'id');
    }

}
