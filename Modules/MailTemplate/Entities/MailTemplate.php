<?php

namespace Modules\MailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $table = 'mail_templates';
    protected $fillable = ['title', 'subject', 'body', 'mail_template_category_id', 'created_by', 'updated_by'];

    public function category() {
        return $this->belongsTo('Modules\MailTemplate\Entities\MailTemplateCategory', 'mail_template_category_id','id');
    }

    public function files() {
        return $this->hasMany('Modules\MailTemplate\Entities\MailTemplateAttachment');
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
