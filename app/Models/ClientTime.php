<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTime extends Model
{
    protected $fillable = ['time','frequency','first_run_time','last_run_time','next_run_time'];
    protected $table = "client_time";

    public function user()
    {
        return $this->belongsTo(User::class,'client_id');
    }
}
