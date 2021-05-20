<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Status\StatusResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserResource;

use App\Http\Resources\Task\TaskResource;
use App\Http\Resources\Ticket\TicketResource;
use Illuminate\Support\Facades\DB;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

     public function getSingleOwner($owners){
         if($owners && $owners->count() > 0){
            return $owners->map(function($owner){
                return $owner->only(['id','name']);
            });
         }
         return [];
      }

   public function getSingleAssign($assigns){
    $assignsArry = array();
    foreach ($assigns as $assign ) {
        array_push($assignsArry, $assign->only(['id','name']));
    }
        return $assignsArry;
    }


    public function returnCreator($created_by){
        if($created_by == null){
            return null;
        }
        else{
            return $created_by->only(['id','name']);
        }
    }

    public function checkFavorite($project_id){

        $favorites = DB::table('project_user')->get();
        $x = 0;
        foreach($favorites as $favorite){

            if(auth()->user()->id == $favorite->user_id && $project_id == $favorite->project_id){
                $x++;
            }
        }
        if($x > 0){
            return true;
        }else{
            return false;
        }

    }

    public function toArray($request)
    {

        if($request->dropdown == "true"){
            return [
                'id'   => $this->id,
                'name' => $this->name,
            ];
        }

        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, $this->description),
            "task_rate" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, $this->task_rate),
            "budget_hours" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, $this->budget_hours),
            "created_at" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, $this->created_at),
            "updated_at" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, $this->updated_at),
            "status" => $this->when(!$request->user_profile,new StatusResource($this->project_status)),
            "created_by" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, $this->returnCreator($this->creator)),
            "updated_by" => $this->when(!$request->index && !$request->financial  && !$request->projectstatus, new UserResource($this->whenLoaded('updater'))),
            "owners" => UserResource::collection($this->owner),
            "project_assign" =>  $this->when(!$request->user_profile,UserResource::collection($this->assigns)),
            "tasks" =>  $this->when(!$request->index && !$request->financial , TaskResource::collection($this->whenLoaded('tasks'))),
            "tickets" => $this->when(!$request->index && !$request->financial , TicketResource::collection($this->whenLoaded('tickets'))),
            "editable"    => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,can_permission('edit','project',$this->created_by)),
            "deletable"   => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,can_permission('delete','project',$this->created_by)),
            'fav' => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,$this->checkFavorite($this->id)),
            'estimated_time' => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,$this->estimated_time),
            'budget' => $this->when(!$request->projectstatus,$this->budget),
            "project_actual_time" => $this->when($request->financial, gmdate("H:i:s", $this->get_actual_time())),
            "project_remaining_time" => $this->when($request->financial, gmdate("H:i:s", $this->get_remaining_time())),
        ];
    }


    public function get_actual_time() {
        $tickets_time = $this->tickets->sum(function($ticket){
            return $ticket->tracking->sum('count_time');
        });

        $tasks_time   = $this->tasks->sum(function($task){
            return $task->tracking_history->sum('count_time');
        });
        return $tickets_time + $tasks_time;
    }


    public function get_remaining_time() {
       return $this->estimated_time - $this->get_actual_time() > 0 ? $this->estimated_time - $this->get_actual_time() : 0;
    }

}
