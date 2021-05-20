<?php

namespace Modules\ItemCategory\Entities;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
  protected $table = 'item_categories';
  protected $fillable = array('name', 'description', 'created_by', 'updated_by');
  public $timestamps = false;


  public function items()
  {
      return $this->hasMany('Modules\Item\Entities\Item');
  }

  public function creator()
  {
      return $this->hasOne('App\Models\User', 'id', 'created_by');
  }

  public function updater()
  {
      return $this->hasOne('App\Models\User', 'id', 'updated_by');
  }
}
