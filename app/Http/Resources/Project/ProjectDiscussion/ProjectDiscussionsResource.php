<?php

namespace App\Http\Resources\Project\ProjectDiscussion;

use App\Http\Resources\User\UserResource;
use App\Models\ProjectDiscussion;
use App\Models\User;
use App\Http\Resources\Project\ProjectDiscussion\ProjectDiscussionRepliesResource;
use Illuminate\Http\Resources\Json\JsonResource;
use function foo\func;

class ProjectDiscussionsResource extends JsonResource
{

    public function getSingleRes($res){
        $resArry = array();
        foreach ($res as $responsible ) {
            array_push($resArry, $responsible->only(['id','name']));
        }
        return $resArry;
    }
    public function getSingleFile($files){
        if($this->files){
            $filesArr = array();
            foreach ($files as $file ) {
                array_push($filesArr, $file->only(['file_path']));
            }
            return $filesArr;
        }
        else{
            return null;
        }

    }



    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "content" => $this->content,
            "created_by" => User::find($this->created_by)->only(['name','id']),
            "project_id" => $this->project_id,
            "responsibles" => $this->getSingleRes($this->responsibles),
            "created_at" => $this->created_at,
            "comments_num" => $this->comments_num,
            "replied" => $this->replied,
            "files" => DiscussionFilesResource::collection($this->files),
//            "owners" => $this->getOwners($this->project_id),
//            "replies" => $this->CheckIfHasReplies($this->id),
            "replies" => ProjectDiscussionRepliesResource::collection($this->replies),
            "seen" => $this->seen,

        ];
    }
}
