<?php

namespace App\Http\Requests\TaskRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;
use App\Exceptions\ItemNotFoundException;

class ViewTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can view tasks

        //1- admin
        if (auth()->user()->isAdmin()) {
            return true;
        }

        $task_id =$this->route('task');
        $task = Task::find($task_id);

        if (!$task) {
            throw new ItemNotFoundException($task_id);
        }

        // 2- responsible && created by && project owner
        if ($task->responsible->id == auth()->user()->id
            || $task->created_by == auth()->user()->id) {
            return true;
        }

        //3- project owner
        if($task->project->owner->count() > 0){
            foreach ($task->project->owner as $owner) {
                if ($owner->id == auth()->user()->id) {
                    return true;
                }
            }
        }


        // 4- assign to project
        foreach ($task->project->assigns as $assign) {
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
