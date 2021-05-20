<?php

namespace Modules\TicketCalendar\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'ticket_id'    => 'required|exists:tickets,id',
            'description' => 'required',
            'title'       => 'required',
        ];
        if(!$this->event)
            $rules['date_time']   ='required';
        return $rules;
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
