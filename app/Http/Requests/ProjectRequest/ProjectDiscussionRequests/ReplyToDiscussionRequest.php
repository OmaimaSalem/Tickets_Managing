<?php

namespace App\Http\Requests\ProjectRequest\ProjectDiscussionRequests;

use Illuminate\Foundation\Http\FormRequest;

class ReplyToDiscussionRequest extends FormRequest
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
            'content' => 'required|string',
            'files' => 'nullable|array',
            'reply' => 'nullable'
        ];
    }
}
