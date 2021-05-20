<?php

namespace Modules\Calender\Entities;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['user_id','calendar_user_id','permissions'];
    protected $table = "calender_settings";

    public function calendars(){
        return $this->hasMany(Calendar::class,'user_id','calendar_user_id');
    }

    public function user(){
        return $this->belongsTo(\App\Models\User::class,'calendar_user_id','id');
    }

}
