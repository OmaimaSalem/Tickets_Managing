<?php

namespace Modules\ProjectFiles\Http\Resources;

use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\Resource;


class FolderResource extends Resource
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
            "description" => $this->description,
            "project_id" => $this->project_id,
            "path" => $this->path,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            'created_by' => $this->returnUploaded_by($this->created_by),

        ];
    }
}
