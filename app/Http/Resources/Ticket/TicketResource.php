<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Project\ProjectResource;
use App\Http\Resources\Setting\SettingResource;
use App\Http\Resources\Status\StatusResource;
use App\Http\Resources\Task\TaskResource;
use App\Http\Resources\Ticket_file\Ticket_fileResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\TicketConversation\Http\Resources\TicketConversationResource;
use Modules\TicketDialog\Http\Resources\CommentResource;
use Modules\TicketDialog\Http\Resources\ReplyResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

       public function returnProject($project){
        if($project == null){
           return null;
        }
        else{
            return $project->only(['name' , 'id','owner']);
        }
    }
//    public function returnManger($manger)
//    {
//        if ($manger == null) {
//            return null;
//        } else {
//            return $manger->only(['name', 'id']);
//        }
//    }

    public function returnManger($mangers){
        if($mangers && $mangers->count() > 0){
            return $mangers->map(function($manger){
                return $manger->only(['id','name','email']);
            });
        }
        return [];
    }

    public function returnOwner($owners){
        if($owners && $owners->count() > 0){
            return $owners->map(function($owner){
                return new UserResource(User::find($owner->id));
            });
        }
        return [];
    }

    public function getSingleFile($files)
    {
        if ($this->files) {
            $filesArr = array();
            foreach ($files as $file) {
                array_push($filesArr, $file->only(['file_path']));
            }
            return $filesArr;
        } else {
            return null;
        }
    }
    public function toArray($request)
    {
        if($request->kanban){
            return [
                "id" => $this->id,
                "name" => $this->name,
                "due_date" =>  $this->due_date,
                "number" => $this->number,
                "pirority" => $this->pirority,
                "status" =>  ['id' => $this->ticket_status->id, 'name' => $this->ticket_status->name] ,
                "project" => $this->project ?  
                    [
                     'id' => $this->project->id, 
                     'name' => $this->project->name, 
                     'owner' => $this->project->owner->map(function($own){
                         return [
                            'id'   => $own->id,
                            'name' => $own->name
                         ];
                     })
                    ] : [],
                "owner" => $this->returnOwner($this->owner),
                "assigns" => $this->whenLoaded('assigns'),
                "old_status_id" => $this->old_status_id
            ];
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" =>  $this->when(!$request->index && !$request->user_profile && !$request->projectstatus, $this->description),
            "due_date" =>  $this->due_date,
            "number" => $this->number,
            "read" => $this->when(!$request->user_profile && !$request->projectstatus,$this->read),
            "reply" => $this->when(!$request->user_profile && !$request->projectstatus,$this->reply),
            "pirority" => $this->when(!$request->user_profile && !$request->projectstatus,$this->pirority),
            "status" => $this->when(!$request->projectstatus , new StatusResource($this->ticket_status)),
            "status_id" => $this->ticket_status->id,
            "files"=> $this->when(!$request->index && !$request->user_profile && !$request->projectstatus, Ticket_fileResource::collection($this->files)),
            "created_at" => $this->when(!$request->projectstatus, $this->created_at),
            "updated_at" => $this->when(!$request->projectstatus, $this->updated_at),
            "created_by" => new UserResource($this->whenLoaded('creator')),
            "updated_by" => new UserResource($this->whenLoaded('updater')),
            "owner" => $this->returnOwner($this->owner),
            //"project" => new ProjectResource($this->whenLoaded('project')),
            "project" => $this->when(!$request->projectstatus, new ProjectResource($this->whenLoaded('project'))),
            "setting" => $this->when(!$request->projectstatus, new SettingResource($this->whenLoaded('setting'))),
            "tracking" => $this->when(!$request->projectstatus, $this->whenLoaded('tracking')),
            "assigns" => $this->whenLoaded('assigns'),
            "tasks" => $this->when($request->tasks, TaskResource::collection($this->tasks)),
            "editable"   =>$this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban, can_permission('edit','ticket',$this->created_by)),
            "deletable"   => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,can_permission('delete','ticket',$this->created_by)),
            // "conversations" => $this->conversations()->count(),
            'manager' => $this->when(!$request->user_profile && !$request->projectstatus,$this->returnManger($this->manager)),
            'collection' => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,$this->collection),
            'estimated_time' => $this->when(!$request->user_profile && !$request->projectstatus && !$request->kanban,$this->estimated_time),
            'cc' => $this->when(!$request->index && !$request->projectstatus , CCResource::collection($this->cc)),
            'ticketDialogFiles' => $this->when(!$request->index && !$request->projectstatus, $this->getSingleFile($this->ticketDialogFiles)),
            "ticketContract" => $this->whenLoaded('ticketContract'),

            // 'replies' =>$this->when(!$request->conversation, ReplyResource::collection($this->replies)),
            // 'comments' =>$this->when(!$request->conversation, CommentResource::collection($this->comments)),
        ];
    }
}
