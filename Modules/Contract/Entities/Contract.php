<?php

namespace Modules\Contract\Entities;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
  protected $table = 'contracts';
    public $timestamps = false;
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
        'created_by',
        'updated_by',
        'setting_id',
        'offer_id',
    );

    public function client()
    {
        return $this->hasOne('App\Models\User','id', 'client_id');
    }

    public function contract_items()
    {
        return $this->hasMany('Modules\ContractItems\Entities\ContractItems');
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

    public function offer()
    {
        return $this->belongsTo('Modules\Offer\Entities\Offer', 'offer_id', 'id');
    }
}
