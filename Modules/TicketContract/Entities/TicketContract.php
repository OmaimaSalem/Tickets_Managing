<?php

namespace Modules\TicketContract\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Ticket;
class TicketContract extends Model
{
    protected $table    =  'ticket_contruct';
    protected $fillable = ['ticket_id','total_price','user_id'];

    public function items(){
       return $this->hasMany(TicketContractItem::class,"ticket_contruct_id","id");
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function ticket(){
        return $this->belongsTo(Ticket::class);
    }
    
 
}
