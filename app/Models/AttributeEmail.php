<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeEmail extends Model
{
   protected $fillable = ['id','attribute_value','attribute_name','attribute_id',
   'mail_start','email_time','email_content','user_type','next_run_time'];

   public function Attribute() {
        return $this->belongsTo(Attribute::class);
   }



}
