<?php

namespace Modules\SpamTickets\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SpamTickets extends Model
{
    protected $fillable = ['name', 'description', 'read', 'created_by', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'id', 'owner_id');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
}
