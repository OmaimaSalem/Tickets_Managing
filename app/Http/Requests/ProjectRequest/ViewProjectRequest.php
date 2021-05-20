<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
use App\Exceptions\ItemNotFoundException;

class ViewProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who view ?

        // 1- admin
        if (auth()->user()->isAdmin() || auth()->user()->can('other-project-list')) {
            return true;
        }

        $project_id =$this->route('project')->id;
        $project = Project::find($project_id);

        if (!$project) {
            throw new ItemNotFoundException($project_id);
        }


        // 2- creator
        if ($project->created_by == auth()->user()->id) {
            return true;
        }

        // 3- owner
        if($project->owner->count() > 0){
            foreach ($project->owner as $owner) {
                if ($owner->id == auth()->user()->id) {
                    return true;
                }
            }
        }


        // 4- assigns
        foreach ($project->assigns as $assign) {
            if ($assign->id == auth()->user()->id) {
                return true;
            }
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
            //
        ];
    }
}
