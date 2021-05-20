<?php

namespace Modules\OfferItems\Entities;

use Illuminate\Database\Eloquent\Model;

class OfferItems extends Model
{
    protected $table = 'offer_items';
    public $timestamps = false;
    protected $fillable = array(
        'name',
        'description',
        'price',
        'tax',
        'tax_price',
        'qty',
        'total_price',
        'total',
        'category',
        'offer_id',
        'created_by',
        'updated_by',
    );

    public function offer()
    {
        return $this->belongsTo('Modules\Offer\Entities\Offer', 'id', 'offer_id');
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
