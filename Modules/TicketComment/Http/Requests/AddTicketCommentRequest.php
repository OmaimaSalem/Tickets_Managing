<?php

namespace Modules\TicketComment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddTicketCommentRequest extends FormRequest
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
            'ticket_id' => 'required|integer|exists:tickets,id',
            'comment' => 'required|min:3|max:4294967295',
            'send_mail' => 'required|boolean',
            'by_client' => 'boolean',
            'reply' => 'required|boolean',
            'mail' => 'nullable|string',
            'subject' => 'nullable|string',
        ];
    }
}
