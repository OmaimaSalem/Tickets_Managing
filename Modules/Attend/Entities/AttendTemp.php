<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

class AttendTemp extends Model
{
    protected $table = "attends_temp";
    protected $fillable = ['id','user_id','date','from','to','day_time','active'];
    
    public function breaks()
    {
        return $this->hasMany(AttendBreaksTemp::class,'attend_id');
    }

}
