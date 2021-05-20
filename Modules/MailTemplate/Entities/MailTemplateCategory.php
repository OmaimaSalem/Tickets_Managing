<?php

namespace Modules\MailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;

class MailTemplateCategory extends Model
{
    protected $table = 'mail_templates_categories';
    protected $fillable = ['name', 'created_by', 'updated_by'];

    public function templates() {
      return $this->hasMany('Modules\MailTemplate\Entities\MailTemplate');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id',  'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }
}
