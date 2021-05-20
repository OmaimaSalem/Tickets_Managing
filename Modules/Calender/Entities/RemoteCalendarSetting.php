<?php

namespace Modules\Calender\Entities;

use Illuminate\Database\Eloquent\Model;

class RemoteCalendarSetting extends Model
{
    protected $fillable    = ['user_id','username','password'];
    protected $table       = "remote_calendars_data";
    public    $timestamps = false;
}
