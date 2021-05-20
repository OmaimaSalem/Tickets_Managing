<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class YearlyVacation extends Model
{
    protected $fillable = ['year','annual','casual'];
}
