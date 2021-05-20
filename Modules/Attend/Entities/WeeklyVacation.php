<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class WeeklyVacation extends Model
{
    protected $fillable = ['year','day'];
}
