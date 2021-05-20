<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
      'name'
    ];
    public function cards()
    {
        return $this->hasMany('Modules\Todo\Entities\Card');
    }

    // public function users() {
    // 	return $this->belongsToMany('App\Models\User');
    // }
}
