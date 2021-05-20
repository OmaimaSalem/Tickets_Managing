<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class YearlyHoliday extends Model
{
    protected $fillable = ['year','name','from','to'];
}
