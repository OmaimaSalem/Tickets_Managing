<?php

namespace Modules\MailTemplate\Entities;

use Illuminate\Database\Eloquent\Model;

class MailTemplateAttachment extends Model
{
    protected $table = 'mail_templates_attachments';
    protected $fillable = ['mail_template_id', 'attachment_path', 'created_by', 'updated_by'];

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id',  'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }

    public function mail_template() {
      return $this->belongsTo('Modules\MailTemplate\Entities\MailTemplate', 'id', 'mail_template_id');
    }
}
