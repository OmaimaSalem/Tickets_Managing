<?php

namespace Modules\TicketContract\Entities;

use Illuminate\Database\Eloquent\Model;

class TicketContractItem extends Model
{
    protected $table    =  'ticket_contruct_items';
    protected $fillable = ['ticket_id','ticket_contruct_id',
                            'item','item_id','item_price',
                            'item_count','item_total_price',
                            'item_vat',
                            'item_net',  
                            'item_discount',    
                            'item_total'
                        ];
}
