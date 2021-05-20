<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\Tickets\CollectionScope;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    use HasRoles;
    use SoftDeletes;

    /**
      * The "booted" method of the model.
      *
      * @return void
      */
      protected static function boot()
      {
          parent::boot();
          static::addGlobalScope('collection', function (Builder $builder) {
              if(!request()->filtered){
                $builder->where('collection', null)->orwhere('collection', '');
              }else {

               if(request()->collection && trim(request()->collection) != "") {
                  $builder->where('collection', request()->collection);
               }else {
                  $builder->where('collection','!=',null)->orwhere('collection','!=', '');
               }
              }
          });
      }




    protected $table = 'tickets';
    public $timestamps = false;
    protected $fillable = array(
        'created_at',
        'updated_at',
        'name',
        'description',
        'project_id',
        'status_id',
        'owner_id',
        'email_id',
        'created_by',
        'updated_by',
        'setting_id',
        'number',
        'column_order',
        'manager_id',
        'due_date',
        'estimated_time'
    );


    public function cc()
    {
        return $this->hasMany(Ticket_mail::class);
    }
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function owner()
    {
        return $this->belongsToMany('App\Models\User','owner_ticket','ticket_id','owner_id');
    }

    public function manager()
    {
        return $this->belongsToMany('App\Models\User','manager_ticket','ticket_id','manager_id');
    }

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function updater()
    {
        return $this->hasOne('App\Models\User', 'id', 'updated_by');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function assigns()
    {
        return $this->belongsToMany('App\Models\User','tickets_assigns');
    }

    public function ticket_status()
    {
        return $this->hasOne('App\Models\Status', 'id', 'status_id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\Ticket_file');
    }

    public function mails()
    {
        return $this->hasMany('App\Models\Ticket_mail');
    }

    public function setting()
    {
        return $this->hasOne('App\Models\Setting', 'id', 'setting_id');
    }

//    public function conversations()
//    {
//        return $this->hasMany('Modules\TicketConversation\Entities\TicketConversation');
//    }

    public function conversationReplies(){
        return $this->hasMany('Modules\TicketDialog\Entities\Reply')->orderBy('created_at','DESC');;
    }
    public function conversationComments(){
        return $this->hasMany('Modules\TicketDialog\Entities\Comment')->orderBy('created_at','DESC');;
    }
    public function ticketDialogFiles(){
        return $this->hasMany('Modules\TicketDialog\Entities\TicketDialogFiles');
    }
    public function returnOldReplies($replyId){
        return  $this->conversationReplies->where('id', '!=' , $replyId);
    }


    public function ownTickets($id)
    {
        return Ticket::with('project.owner')
                    ->select('tickets.*')
                    ->join('projects', 'tickets.project_id', 'projects.id')
                    ->join('project_assigns', 'project_assigns.project_id', '=', 'projects.id')
                    ->where('project_assigns.assign_to', $id);
//                    ->orWhere('tickets.created_by', $id);
//                    ->paginate();

    }

    public function tracking()
    {
        return $this->hasMany('App\Models\TrackTicket');
    }


    public function all_time() {
        $tasks_time =  Ticket::select(\DB::raw('SUM(tracking_tasks.count_time) AS tasks_time'))
                ->join('tasks','tasks.ticket_id','tickets.id')
                ->join('tracking_tasks','tracking_tasks.task_id','tasks.id')
                ->where('tickets.id', $this->id)
                ->groupby('tickets.id')
                ->first();

        $ticket_time = 0;



        $unfinished = $this->tracking()->whereNull('end_at')->first();
        if($unfinished && $unfinished->count() > 0){
            $ticket_time = time() - strtotime($unfinished->start_at);
        } else {
            $ticket_time =  $this->tracking()->sum('count_time');
        }


        return $ticket_time + ($tasks_time ? $tasks_time->tasks_time : 0);
    }

    public function copy(){
        $new = $this->replicate();
        $new->save();
        $new->assigns()->sync($this->assigns);
        return $new;
    }


    public function events()
    {
        return $this->hasMany('Modules\TicketCalendar\Entities\Event');
    }



    public function ticketContract()
    {
       return $this->HasOne('Modules\TicketContract\Entities\TicketContract');
    }


}
