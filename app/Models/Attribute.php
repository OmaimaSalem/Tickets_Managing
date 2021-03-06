<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['user_type','name', 'type','values'];
    protected $casts = [
        'values' => 'array'
    ];

}
