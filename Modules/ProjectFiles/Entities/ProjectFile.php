<?php

namespace Modules\ProjectFiles\Entities;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    protected $fillable = [
        'name',
        'path',
        'type',
        'project_id',
        'folder_id',
        'uploaded_by',
    ];

    public function folder(){
        return $this->belongsTo('Modules\ProjectFile\Entities\ProjectFolder');
    }
    public function project(){
        return $this->belongsTo('App\Models\Project');
    }
    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'uploaded_by');
    }
}
