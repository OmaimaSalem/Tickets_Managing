<?php

namespace Modules\Calender\Entities;

use Illuminate\Database\Eloquent\Model;

class CalendarFreeTime extends Model
{
    protected $fillable = ['day','day_number','from','to'];
}
