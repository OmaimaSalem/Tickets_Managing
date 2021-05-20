<?php

namespace Modules\TicketCalendar\Entities;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['date_time','title','description'];
    protected $table = "ticket_events";


    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket');
    }
}
