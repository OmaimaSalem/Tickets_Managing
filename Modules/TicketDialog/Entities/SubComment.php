<?php

namespace Modules\TicketDialog\Entities;

use Illuminate\Database\Eloquent\Model;

class SubComment extends Model
{
    protected $table = 'ticket_sub_comments';
    protected $fillable = ['comment_id', 'created_by', 'content'];

    public function ParentComment(){
    	return $this->belongsTo('Modules\TicketDialog\Entities\Comment', 'id', 'comment_id');
    }

    public function creator(){
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
}
