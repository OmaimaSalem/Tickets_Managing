<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class Attend extends Model
{
    protected $fillable = ['date','from','to','day_time','active'];

    public function breaks()
    {
        return $this->hasMany(AttendBreaks::class);
    }
    public function projects()
    {
        return $this->hasMany(AttendProjects::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
