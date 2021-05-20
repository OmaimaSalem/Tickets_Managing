<?php

namespace Modules\TicketDialog\Entities;

use Illuminate\Database\Eloquent\Model;

class TicketSubReplyCC extends Model
{
    protected $table = 'ticket_sub_replies_cc';
    protected $fillable = ['sub_reply', 'created_by', 'updated_by', 'email'];

    public function reply(){
        return $this->belongsTo('Modules\TicketDialog\Entities\Reply', 'id', 'sub_reply');
    }

    public function creator(){
      return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function updater(){
      return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }
}
