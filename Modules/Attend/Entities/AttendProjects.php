<?php

namespace Modules\Attend\Entities;

use Illuminate\Database\Eloquent\Model;

use App\Models\Project;

class AttendProjects extends Model
{
    protected $fillable = ['project_id','from','to','work_time'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
