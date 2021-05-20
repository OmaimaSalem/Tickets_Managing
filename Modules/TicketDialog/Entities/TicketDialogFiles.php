<?php

namespace Modules\TicketDialog\Entities;

use Illuminate\Database\Eloquent\Model;

class TicketDialogFiles extends Model
{
	protected $table = 'ticket_dialog_files';
    public $timestamps = false;
    protected $fillable = array('ticket_id', 'file_path');

    public function ticket(){
        return $this->belongsTo('App\Models\Ticket');
    }


}
