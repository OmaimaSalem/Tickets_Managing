<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackTicket extends Model
{
    protected $table = "tracking_tickets";
    protected $fillable = ['start_at','end_at','user_id','count_time','ticket_id','reason'];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

}
