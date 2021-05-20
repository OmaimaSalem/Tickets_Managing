<?php

namespace Modules\Offer\Entities;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';
    public $timestamps = true;
    protected $fillable = array(
        'number',
        'client_id',
        'date',
        'total',
        'greeting',
        'street',
        'country',
        'city',
        'first_name',
        'postal_code',
        'telephone',
        'mobile',
        'title',
        'name',
        'fax',
        'email',
        'others',
        'notes',
        'printed_text',
        'contract_id',
        'created_by',
        'updated_by',
        'setting_id',
        'total_tax',
    );

    public function client()
    {
        return $this->hasOne('App\Models\User','id', 'client_id');
    }

    public function contract()
    {
        return $this->hasOne('Modules\Contract\Entities\Contract','offer_id', 'id');
    }

    public function offer_items()
    {
        return $this->hasMany('Modules\OfferItems\Entities\OfferItems');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }

    public function setting()
    {
        return $this->hasOne('App\Models\Setting', 'id', 'setting_id');
    }
}
