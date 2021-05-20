<?php

namespace App\Http\Resources\Task;

use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Receipt\ReceiptResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Ticket\LightTicketResource;
use App\Http\Resources\Ticket\TicketResource;
use App\Http\Resources\Tracking\TrackingResource;
use App\Http\Resources\User\UserResource;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Http\Resources\Json\JsonResource;
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *git
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function returnProject($project){
        if($project == null){
           return [];
        }
        else{
            return new ProjectResource(Project::find($project->id));
        }
    }

    public function returnTicket($ticket){
        if($ticket == null){
            return [];
        }
        else{
            return new LightTicketResource(Ticket::find($ticket->id));
        }
    }
    public function returnResponsible($responsible){
        if($responsible == null){
            return [];
        }
        else{
            return $responsible->only(['name' , 'id']);
        }
    }
    public function returnCreator($creator){
        if($creator == null){
            return [];
        }
        else{
            return $creator->only(['name' , 'id']);
        }
    }

    public function getSingleOwner($owners){
         if($owners && $owners->count() > 0){
            return $owners->map(function($owner){
                return $owner->only(['id','name']);
            });
         }
         return [];
      }

    public function returnClient($task){

        if($task->project && !$task->ticket){
            return $task->project->owner->first() ? $task->project->owner->first()->only(['name','id']) : null;
        }
        else if($task->ticket && !$task->project){
            return $task->ticket->owner ? $task->ticket->owner->only(['name','id']) : null;
        }
        else if($task->ticket && $task->project){
            return $task->ticket->owner ? $task->ticket->owner->only(['name','id']) : null;
        }

    }


    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->when(!$request->projectstatus && !$request->user_profile , $this->description),
            "count_hours" => $this->when(!$request->projectstatus ,$this->count_hours),
            "priority" =>    $this->when(!$request->projectstatus , $this->priority),
            "start_at" => $this->when(!$request->projectstatus ,$this->start_at),
            "deadline" => $this->when(!$request->projectstatus ,$this->deadline),
            "status" => $this->when(!$request->projectstatus ,new StatusResource($this->task_status)),
            "status_id" => $this->task_status->id,
            "created_at" => $this->when(!$request->projectstatus ,$this->created_at),
            "updated_at" => $this->when(!$request->projectstatus ,$this->updated_at),
            // "owner" => $this->getSingleOwner($this->project->owner),
            "created_by" => $this->when(!$request->projectstatus ,$this->returnCreator($this->creator)),
            //"updated_by" => $this->whenLoaded('updater')->only(['id','name']),
            //"ticket" => new TicketResource($this->whenLoaded('ticket')),
            "ticket" => $this->when(!$request->projectstatus ,$this->returnTicket($this->ticket)),
            //"ticket" => $this->whenLoaded('ticket')->only(['id','name']),

            //"project" => new ProjectResource($this->whenLoaded('project')),
            //"project" => $this->whenLoaded('project')->only(['id','name']),
            "project" => $this->when(!$request->projectstatus ,$this->returnProject($this->project)),
            "responsible" => $this->when(!$request->projectstatus ,$this->returnResponsible($this->responsible)),
            "receipts" => $this->when(!$request->projectstatus ,ReceiptResource::collection($this->whenLoaded('receipts'))),
            "tracking_history" => $this->when(!$request->projectstatus ,TrackingResource::collection($this->whenLoaded('tracking_history'))),
            'estimated_time' => $this->estimated_time,
            'old_status' => $this->old_status,
            'time_in_hours' => round(($this->tracking_history->sum('count_time') / 3600),2)
        ];





    }


}
