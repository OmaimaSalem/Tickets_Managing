<?php

namespace App\Http\Requests\TicketRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Project;
use App\Exceptions\ItemNotFoundException;
use Illuminate\Http\Request;

class AddTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can add ticket ??

        // 1- admin
        if (auth()->user()->isAdmin() || auth()->user()->can('ticket-create') ) {
            return true;
        }
        if($this->route('project_id')) {
            $project_id =$this->route('project_id');
            $project = Project::find($project_id);

            if($project) {
                // 2- project owner
                if($project->owner && $project->owner->count() > 0) {
                    foreach ($project->owner as $owner) {
                       if ($owner->id == auth()->user()->id) {
                            return true;
                        }
                    }
                }

                if($project->assigns && $project->assigns->count() > 0) {
                    // 3- people who are assign to project
                    foreach ($project->assigns as $assign) {
                        if ($assign->id == auth()->user()->id) {
                            return true;
                        }
                    }
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
    public function rules(Request $request)
    {
        $rules =   [
            'name' => 'required|string',
            'description' => 'string',
            'project_id' => 'nullable|integer|exists:projects,id',
            'status_id' => 'required|integer|exists:status,id',
            'due_date' => '',
            'owner_id' => 'required|array',
            'owner_id.*' => 'required|exists:users,id',
            'manager_id' => 'required|array',
            'manager_id.*' => 'required|exists:users,id',
            'estimated_time' => 'nullable|integer'
        ];

        if($request->kanban || $request->direction){
            $rules =  [
                'name' => 'nullable|string',
                'description' => 'nullable|string',
                'project_id' => 'nullable|integer|exists:projects,id',
                'status_id' => 'nullable|integer|exists:status,id',
                'client' => 'nullable',
                'due_date' => 'nullable',
                'owner_id' => 'nullable|array',
                'owner_id.*' => 'nullable|exists:users,id',
                'manager_id' => 'nullable|array',
                'manager_id.*' => 'nullable|exists:users,id',
                'estimated_time' => 'nullable|integer'
            ];
        }

        return $rules;


    }

    public function messages(){
        return [
            'owner_id.required' => 'the client field is required',
            'owner_id.*.exists' => 'the client field is invalid',
            'manager_id.required' => 'the manager field is required',
            'manager_id.*.exists' => 'the manager field is invalid',
        ];
    }
}
