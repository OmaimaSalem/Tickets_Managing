<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Metadata\MetadataResource;
use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Ticket\TicketResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Todo\Http\Resources\BoardResource;
use App\Http\Resources\Attribute\AttributeResource;
use App\Http\Resources\Attribute\TicketAttributesResource;
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(($request->dropdown && $request->dropdown=="true") || ($request->user_dropdown && $request->user_dropdown=="true")){
            return [
             "id" => $this->id,
             "name" => $this->name,
            ];
        }

        $data = [
            "id" => $this->id,
            "name" => $this->name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "mobile" => $this->mobile,
            "address" => $this->address,

            "email" => $this->when(!$request->index && !$request->projectstatus, $this->email),
            "created_at" => $this->when(!$request->index && !$request->projectstatus, $this->created_at),
            "updated_at" => $this->when(!$request->index && !$request->projectstatus, $this->updated_at),
            "roles" => $this->when(!$request->index && !$request->projectstatus && !$request->kanban, RoleResource::collection($this->whenLoaded('roles'))),
            "type" => $this->when(!$request->index && !$request->projectstatus, $this->type),
            "vacation" =>  $this->when(!$request->index && !$request->projectstatus, $this->vacation),
            "skip_vacation_limit" => $this->when(!$request->index && !$request->projectstatus, $this->skip_vacation_limit),
            "metadata" => $this->when(!$request->index && !$request->projectstatus, new MetadataResource($this->whenLoaded('metadata'))),
            "projects" => $this->when(!$request->index, ProjectResource::collection($this->whenLoaded('projects'))),

            "attributes" => $this->when(!$request->kanban , TicketAttributesResource::collection($this->attributes)),
            // // "ticket_tracking" => $this->when($request->time, $this->ticket_tracking),
            // // "task_tracking" => $this->when($request->time,$this->task_tracking),
            "working_time" => $this->get_all_time(),
            'client_time' => $this->when(!$request->index && !$request->projectstatus && !$request->kanban , $this->client_time),
            "spam" =>  $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban ,$this->spam),
            "support" =>  $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban ,$this->support),
            // // "created_at" => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y')
            //"boards" => BoardResource::collection(),
        ];


        if($request->time){
            $data["total_time"] = $this->total_time($this->ticket_tracking,$this->task_tracking);
            $data["tasks"] =   $this->get_tasks($this->task_tracking)->unique('id')->filter(function($value, $key) {
                return  $value != null;
            });
            $data["tickets" ] =$this->get_tickets($this->ticket_tracking)->unique('id')->filter(function($value, $key) {
                return  $value != null;
            });
            $data["time_projects"] = $this->when(!$request->projectstatus, $this->get_projects($this->ticket_tracking,$this->task_tracking));
        }


        return $data;
    }

    public function total_time($ticket_time,$task_time) {
        return gmdate("H:i:s",$ticket_time->sum('count_time') + $task_time->sum('count_time'));
    }

    public function get_tasks($task_tracking) {
        return $this->task_tracking->map(function($ttracking){
            return $ttracking->task;
        });
    }

    public function get_tickets($ticket_tracking) {
        return $this->ticket_tracking->map(function($ttracking){
            return $ttracking->ticket;
        });
    }


    public function get_projects($ticket_tracking,$task_tracking) {
        $tickets = $this->get_tasks($ticket_tracking);
        $tasks   = $this->get_tickets($task_tracking);



        $tickets_projects = $tickets->map(function($ticket){
            return $ticket ? $ticket->project : null;
        })->toArray();

        $tasks_projects = $tasks->map(function($task){
            return $task ? $task->project : null;
        })->toArray();

        $projects = array_merge($tickets_projects,$tasks_projects);
        return collect(array_filter($projects))->unique()->toArray();
    }



}
