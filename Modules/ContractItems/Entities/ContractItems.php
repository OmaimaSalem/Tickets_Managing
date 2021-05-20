<?php

namespace Modules\ContractItems\Entities;

use Illuminate\Database\Eloquent\Model;

class ContractItems extends Model
{
  protected $table = 'contract_items';
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
        'contract_id',
        'created_by',
        'updated_by',
    );

    public function contract()
    {
        return $this->belongsTo('Modules\Contract\Entities\Contract', 'id', 'contract_id');
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
