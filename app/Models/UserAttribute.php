<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttribute extends Model
{
    protected $fillable = ['attribute_id','value','type'];
    protected $casts = [
        'value' => 'array'
    ];


    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }


}
