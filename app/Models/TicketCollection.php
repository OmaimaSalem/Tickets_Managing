<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class TicketCollection extends Model
{
    protected $fillable = ['email','collection'];
    protected $table = "ticket_collection";
}
