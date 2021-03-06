<?php

namespace App\Http\Requests\TrackingRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;
use App\Exceptions\ItemNotFoundException;

class AddTrackingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can start task tracking?
        $task_id =$this->route('task_id');
        $task = Task::find($task_id);
        if (!$task) {
            throw new ItemNotFoundException($task_id);
        }


        // only responsible
        if (($task->responsible && $task->responsible->id == auth()->user()->id) || auth()->user()->isAdmin()) {
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
        $task_id =$this->route('task_id');

        return [
            'comment' => 'required|string',
            'start_at' => 'required|date_format:d-m-Y H:i:s',
            'end_at' => 'date_format:d-m-Y H:i:s',
            'task_id' => 'required|integer|exists:tasks,id|in:'.$task_id
        ];
    }
}
