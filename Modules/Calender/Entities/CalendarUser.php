<?php

namespace Modules\Calender\Entities;

use Illuminate\Database\Eloquent\Model;

class CalendarUser extends Model
{
    protected $fillable = ['user_id','event_id'];

    public function user(){
       return  $this->belongsTo(\App\Models\User::class,'user_id','id');
    }
}
