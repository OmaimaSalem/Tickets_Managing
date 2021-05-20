<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
use App\Exceptions\ItemNotFoundException;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can update ??

        // 1- admin
        // if (auth()->user()->isAdmin()) {
        //     return true;
        // }

        $project =$this->route('project');
        $project = Project::find($project->id);
        if($project && can_permission('edit','project',$project->created_by)) {
            return true;
        }

        if (!$project) {
            throw new ItemNotFoundException($project_id);
        }


        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
            'description' => 'string',
            'owner' => 'array|required',
            'owner.*' => 'required|integer|exists:users,id',
            'project_assign' => 'array',
            'project_assign.*' => 'integer|exists:users,id',
            'status_id' => 'nullable|integer|exists:status,id',
            'estimated_time' => 'nullable|integer',
            'budget' => 'nullable|numeric',
        ];
    }
}
