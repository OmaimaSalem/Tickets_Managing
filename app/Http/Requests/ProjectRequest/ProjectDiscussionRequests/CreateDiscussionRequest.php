<?php

namespace App\Http\Requests\ProjectRequest\ProjectDiscussionRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiscussionRequest extends FormRequest
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
            'title' => 'required|string',
            'content' => 'nullable|string',
            'responsilbes' => 'required|array',
            'responsilbes.*' => 'integer|exists:users,id',
            'files' => 'nullable|array'
        ];
    }



    public function messages(){
        return [
            'responsilbes.required' => 'Client field is required',
            'responsilbes.exits' => 'Client field is invalid',
        ];
    }
}
