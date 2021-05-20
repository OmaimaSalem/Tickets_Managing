<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

use App\Models\Project;

class Vacation extends Model
{
    protected $table = "user_vactions";
    protected $fillable = ['day_from','day_to','time_from','time_to','reason','status','priority','sick_vacation','sick_image'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

}
