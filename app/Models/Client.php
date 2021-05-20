<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name', 'email', 'password', 'vacation','skip_vacation_limit',
        'debitor_number',
        'customer_number',
        'company',
        'addition_company',
        'address',
        'first_name',
        'last_name',
        'gender',
        'telephone',
        'mobile',
        'fax',
        'website',
        'birth_date',
        'eBay_user',
        'tax_number',
        'tax_id',
        'commerical_register',
        'street_number',
        'additional_address',
        'postal_code',
        'city_code',
        'country',
        'state',
        'customer_from',
        'customer_group',
        'first_contact_by',
        'customer_of_company',
        'language',
        'print_templates_set',
        'payment_deadline',
        'payment',
        'discount',
        'description',
        'signature',
        'position',
        'timezone'
    ];

    public function client_time(){
        return $this->hasOne('App\Models\ClientTime','client_id', 'id');
    }
}
