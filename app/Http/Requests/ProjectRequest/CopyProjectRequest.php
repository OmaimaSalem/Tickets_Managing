<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;

class CopyProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:projects,name',
            'description' => 'required|string',
            'owners' => 'array|required',
            'owners.*' => 'required|integer|exists:users,id',
            'status_id' => 'required|integer|exists:status,id',
            'task_rate' => 'required|integer',
            'budget_hours' => 'required|integer',
            'assigns' => 'array',
            'assigns.*' => 'integer|exists:users,id',
            'estimated_time' => 'integer',
        ];
    }
}
