<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasRoles;
    use SoftDeletes;

    protected $table = 'tasks';
    public $timestamps = false;
    protected $fillable = array('created_at', 'updated_at', 'name', 'description', 'responsible_id', 'created_by','owner_id', 'updated_by', 'ticket_id', 'project_id','count_hours', 'status_id', 'priority', 'deadline', 'order_column', 'estimated_time');

    public function responsible()
    {
        return $this->hasOne('App\Models\User', 'id', 'responsible_id');
    }

    public function owner()
    {
        return $this->hasOne('App\Models\User', 'id', 'owner_id');
    }

    public function status()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status_id');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function receipts()
    {
        return $this->hasMany('App\Models\Receipt');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket');
    }

    public function tracking_history()
    {
        return $this->hasMany('App\Models\Tracking_task');
    }

    public function task_status()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status_id');
    }

    public function ownTasks($id)
    {
        return Task::with('project.owner', 'ticket', 'responsible', 'task_status')
                    ->select('tasks.*')
                    ->where('tasks.responsible_id', $id)
                    ->orWhere('tasks.created_by', $id);

    }

}
