<?php

namespace Modules\TicketDialog\Entities;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'ticket_dialog_comments';
    protected $fillable = ['ticket_id', 'created_by', 'content'];

    public function ticket(){
        return $this->belongsTo('App\Models\Ticket');
    }

    public function subComments(){
    	return $this->hasMany('Modules\TicketDialog\Entities\SubComment');
    }

    public function creator(){
    	return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

}
