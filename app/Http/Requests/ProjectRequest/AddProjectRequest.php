<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;

class AddProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // all who has permission
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
            'name' => 'required|string',
            'description' => 'required|string',
            'owner' => 'array|required',
            'owner.*' => 'required|integer|exists:users,id',
            'status_id' => 'required|integer|exists:status,id',
            'project_assign' => 'array',
            'project_assign.*' => 'integer|exists:users,id',
            'estimated_time' => 'nullable|integer',
            'budget' => 'nullable|numeric',

        ];
    }

    public function messages(){
        return [
            'owner.required' => 'The Client field is required.',
            'owner.*.exists' => 'The Client field is invalid.'
        ];
    }
}
