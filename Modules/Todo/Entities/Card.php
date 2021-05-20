<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
      'name',
      'board_id'
    ];

    public function tasks()
    {
        return $this->hasMany('Modules\Todo\Entities\Task')->orderBy('id');
    }


    public function board()
    {
        return $this->belongsTo('Modules\Todo\Entities\Board');
    }
}
