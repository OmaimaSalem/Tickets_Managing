<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use DB;
use Modules\Calender\Entities\Setting;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'type','vacation','skip_vacation_limit',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function projects()
    {
        return $this->belongsToMany('App\Models\Project');
    }

    public function projects_assigns_to()
    {
        return $this->belongsToMany('App\Models\Project','project_assigns','assign_to','project_id');
    }


    public function allowed_categories() {
        return $this->belongsToMany('Modules\Category\Entities\Category');
    }

    public function isAdmin()
    {
        foreach ($this->roles()->get() as $role)
        {
            if ($role->name == 'Full Administrator')
            {
                return true;
            }
        }

        return false;
    }

    public function metadata()
    {
        return $this->hasOne('App\Models\Metadata');
    }

    public function dynamicAttributes()
    {
        return $this->belongsToMany('App\Models\DynamicAttribute','user_dynamic_attributes', 'user_id', 'dynamic_attribute_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function attendes()
    {
        return $this->hasMany('Modules\Attend\Entities\Attend');
    }

   public function attend_data()
   {
        return $this->hasOne('Modules\Attend\Entities\AttedData');
   }

    public function assigns()
    {
        return $this->belongsToMany('App\Models\Project','project_assigns','assign_to','project_id');
    }

    public function vacations()
    {
        return $this->hasMany('Modules\Attend\Entities\Vacation');
    }

    public function getAllPermissionsAttribute() {
      $permissions = [];
      $dbPermissions = Permission::all();
      if($dbPermissions) {
        foreach ($dbPermissions as $permission) {
          if (Auth::user()->can($permission->name)) {
            $permissions[] = $permission->name;
          }
        }
        return $permissions;
      } else {
        return $permissions = ['noPermission'];
      }
    }
    public function getAllRolesAttribute() {
      $roles = [];
      $dbRoles = Role::all();
      if($dbRoles) {
        foreach ($dbRoles as $role) {
          if (Auth::user()->hasRole($role->name)) {
            $roles[] = $role->name;
          }
        }
        return $roles;
      } else {
        return $roles = ['noRole'];
      }
    }

    // public function boards() {
    //   return $this->hasMany('Modules\Todo\Entities\Board');
    // }


    public function attributes()
    {
        return $this->hasMany(UserAttribute::class);
    }

     //many to many relation with tha tasks in the todo module

    public function tasks(){
        return $this->hasMany('Modules\Todo\Entities\Task');
    }


    public function r_tasks(){
        return $this->hasMany('App\Models\Task','responsible_id');
    }

    public function tickets(){
        return $this->belongsToMany('App\Models\Ticket','owner_ticket','ticket_id','owner_id');
    }


    public function r_tickets(){
        return $this->belongsToMany('App\Models\Ticket','manager_ticket','manager_id','ticket_id');
    }


    public function favorites(){
        return $this->belongsToMany('App\Models\Project','project_user', 'user_id', 'project_id');
    }

    public function client_time(){
        return $this->hasOne('App\Models\ClientTime','client_id', 'id');
    }

    public function client_tasks()
    {
        return $this->belongsToMany(Task::class,'task_user','user_id','task_id');
    }

    public function client_tickets()
    {
        return $this->belongsToMany(Ticket::class,'owner_ticket','owner_id','ticket_id');
    }
    public function folders(){
        return $this->hasMany('Modules\ProjectFiles\Entities\ProjectFolder');
    }
    public function files(){
        return $this->hasMany('Modules\ProjectFiles\Entities\ProjectFile');
    }


    function task_tracking()
    {
        return $this->hasMany(Tracking_task::class,'created_by','id');
    }
    function ticket_tracking()
    {
        return $this->hasMany(TrackTicket::class);
    }

    function get_all_time(){
        return gmdate("H:i:s",$this->task_tracking->sum('count_time') + $this->ticket_tracking->sum('count_time'));
    }

    // public function get_all_time() {

    //     $month = request()->month ? request()->month : date('m');

    //     $sql = "SELECT us.id AS user_id,us.name AS user_name,
    //                    pr.id AS project_id,pr.name AS project_name,
    //                    ti.id AS ticket_id,ti.name AS ticket_name,
    //                    ts.id AS task_id,ts.name AS task_name,
    //                    sum(tats.count_time) AS tasks_time,tats.created_at as task_created,
    //                    sum(tatt.count_time) AS tickets_time,tatt.created_at as ticket_created
    //             FROM users us
    //                     LEFT JOIN project_assigns pas ON pas.assign_to = us.id
    //                     LEFT JOIN projects pr ON pr.id = pas.project_id
    //                     LEFT JOIN tickets_assigns tas ON tas.user_id = us.id
    //                     LEFT JOIN tickets ti ON ti.id = tas.ticket_id
    //                     LEFT JOIN tasks ts ON ts.responsible_id = us.id
    //                     LEFT JOIN tracking_tasks tats ON tats.task_id = ts.id
    //                     LEFT JOIN tracking_tickets tatt ON tatt.ticket_id = ti.id
    //                     WHERE us.id = ? AND (pr.id IN (
    //                      SELECT project_id FROM tickets where id in
    //                         (SELECT ticket_id FROM tracking_tickets WHERE month(created_at) = ? )
    //                     ) OR (
    //                         pr.id IN (
    //                         SELECT project_id FROM tasks where id in
    //                             (SELECT task_id FROM tracking_tasks WHERE month(created_at) = ? )
    //                         )
    //                     ))";




    //     $query = DB::select($sql,[$this->id,$month,$month,$month,$month]);


    //     $array = [];
    //     foreach ($query as $row) {

    //         if(!isset($array[$row->user_id]))
    //         {
    //             $array[$row->user_id] = [
    //                 'user_id' => $row->user_id,
    //                 'user_name' => $row->user_name,
    //             ];
    //         }




    //             if(!isset($array[$row->user_id]['projects']))
    //                         {
    //                             $array[$row->user_id]['projects'] = [];
    //                         }

    //                         if(!isset($array[$row->user_id]['projects'][$row->project_id])){
    //                             if($row->project_id && $row->project_name){
    //                                 $array[$row->user_id]['projects'][$row->project_id] = [
    //                                     'project_id' => $row->project_id,
    //                                     'project_name' => $row->project_name
    //                                 ];
    //                             }
    //                         }

    //                         if(!isset($array[$row->user_id]['tickets']))
    //                         {
    //                             $array[$row->user_id]['tickets'] = [];
    //                         }


    //                         if(!isset($array[$row->user_id]['tickets'][$row->ticket_id])){
    //                             if($row->ticket_id && $row->ticket_name){
    //                                 $array[$row->user_id]['tickets'][$row->ticket_id] = [
    //                                     'ticket_id' => $row->ticket_id,
    //                                     'ticket_name' => $row->ticket_name
    //                                 ];
    //                             }
    //                         }



    //                         if(!isset($array[$row->user_id]['tasks']))
    //                         {
    //                             $array[$row->user_id]['tasks'] = [];
    //                         }

    //                         if(!isset($array[$row->user_id]['tasks'][$row->task_id])){
    //                             if($row->task_id && $row->task_name){
    //                                 $array[$row->user_id]['tasks'][$row->task_id] = [
    //                                     'task_id' => $row->task_id,
    //                                     'task_name' => $row->task_name
    //                                 ];
    //                             }
    //                         }

    //                             $array[$row->user_id]['full_time'] =  gmdate("H:i:s", $row->tickets_time + $row->tasks_time);



    //                         }







    //     return $array;

    // }


    public function get_show_calendars() {
        return Setting::with('user:name,id')->where('user_id',auth()->user()->id)->get();
    }

    public function receivesBroadcastNotificationsOn() {
        return 'user-'.$this->id;
    }

}
