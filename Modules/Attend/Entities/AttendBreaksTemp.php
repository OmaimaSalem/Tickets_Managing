<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class AttendBreaksTemp extends Model
{
    protected $table    = "attend_breaks_temp";
    protected $fillable = ['from','to','break_time'];
}
