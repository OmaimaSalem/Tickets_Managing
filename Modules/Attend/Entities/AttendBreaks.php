<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class AttendBreaks extends Model
{
    protected $fillable = ['from','to','break_time'];
}
