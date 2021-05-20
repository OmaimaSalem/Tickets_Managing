<?php

namespace Modules\ProjectFiles\Entities;

use Illuminate\Database\Eloquent\Model;

class ProjectFolder extends Model
{
    protected $fillable = [
        'name',
        'description',
        'path',
        'project_id',
        'created_by',
    ];

    public function file() {
        return $this->hasMany('Modules\ProjectFile\Entities\ProjectFile');
    }
    public function project(){
        return $this->belongsTo('App\Models\Project');
    }
    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
}
