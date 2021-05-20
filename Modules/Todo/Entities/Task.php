<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  protected $table = 'task';

    protected $fillable = [
      'name',
      'description',
      'status',
      'card_id',
      'user_id',
      'deadline',
      'last_time_work',
      'next_time_work'
    ];

    public function Card()
    {
        return $this->belongsTo('Modules\Todo\Entities\Card');
    }

    //a many to many relation with the user model
    public function users(){
        return $this->belongsToMany('App\Models\User');
    }
}
