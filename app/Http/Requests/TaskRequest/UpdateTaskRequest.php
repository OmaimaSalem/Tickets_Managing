<?php

namespace App\Http\Requests\TaskRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;
use App\Exceptions\ItemNotFoundException;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can update task

        // 1- admin
        if (auth()->user()->isAdmin()) {
            return true;
        }

        $task_id =$this->route('task');
        $task = Task::find($task_id);

        if (!$task) {
            throw new ItemNotFoundException($task_id);
        }

        // 2- created by
        if ($task->created_by == auth()->user()->id || $task->responsible_id == auth()->user()->id) {
            return true;
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
