<?php

namespace Modules\ProjectFiles\Http\Resources;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;

class FilesResource extends Resource
{

    public function returnUploaded_by($user){
        if($user != null) {
            return User::find($user)->only(['id','name']);
        }
        return [];
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "path" => $this->path,
            "type" => $this->type,
            "project_id" => $this->project_id,
            "folder_id" => $this->folder_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
//            "uploaded_by" => $this->uploaded_by,
            'uploaded_by' => $this->returnUploaded_by($this->uploaded_by),

        ];
    }
}
