<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class AttedData extends Model
{
    protected $table    = "user_attend_data";
    protected $fillable = ['user_id','start','end'];
}
