<?php

namespace Modules\Calender\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Calender\Entities\Calender;

class Calendar extends Model
{
    protected $table = "calendar";
    protected $fillable = ['user_id','category_id','title','date','from','to','all_day',
    'location','description','creator_id','status','day_name','repeat','repeat_type','end_at','href','etag','name','email','mobile','approved','attachment',
    'attachment_mime_type','attachment_name'];

    public function attendees()
    {
        return $this->hasMany(CalendarUser::class,'event_id','id');

    }
}
