<?php

namespace Modules\TicketDialog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubReplieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reply_id' => 'required|integer',
            'content' => 'required|string',
            'subject' => 'required|string',
            'bcc' => 'nullable',
            'cc' => 'nullable',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
