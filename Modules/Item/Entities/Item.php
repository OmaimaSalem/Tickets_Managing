<?php

namespace Modules\Item\Entities;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    public $timestamps = false;
    protected $fillable = array('created_by', 'updated_by', 'name', 'description', 'price', 'item_category_id');

    public function category()
    {
        return $this->belongsTo('Modules\ItemCategory\Entities\ItemCategory');
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
