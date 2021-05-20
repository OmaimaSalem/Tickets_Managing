<?php

namespace App\Http\Requests\TaskRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
use App\Exceptions\ItemNotFoundException;

class AddTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can add new task?
        // 1- admin
        if (auth()->user()->isAdmin() || auth()->user()->can('task-create')) {
            return true;
        }

        if($this->route('project_id')){
            $project_id =$this->route('project_id');
            $project = Project::find($project_id);

            if (!$project) {
                throw new ItemNotFoundException($project_id);
            }

            //2- people who is assign to this projects
            foreach ($project->assigns as $assign) {
                if ($assign->id == auth()->user()->id) {
                    return true;
                }
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
            'name' => 'required|string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|integer|exists:projects,id',
            'ticket_id' => 'nullable|integer|exists:tickets,id',
            'responsible_id' => 'nullable|integer|exists:users,id',
            'status_id' => 'integer|exists:status,id',
            'count_hours' => 'nullable|numeric|min:0',
            'priority' => 'string',
            'deadline' => 'nullable|date',
            'start_at' => 'date_format:"d-m-Y"',
            'estimated_time' => 'nullable|integer'
        ];
    }
}
