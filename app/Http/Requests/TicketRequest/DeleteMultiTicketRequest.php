<?php

namespace App\Http\Requests;

use App\Exceptions\ItemNotFoundException;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class DeleteMultiTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $tickets = $this->request;
        foreach ($tickets as $ticket){
            $ticket = Ticket::withoutGlobalScopes(['collection'])->find($ticket);


            if($ticket && can_permission('delete','ticket',$ticket->created_by)){
                return true;
            }

            if (!$ticket) {
                throw new ItemNotFoundException($this->route('ticket'));
            }

            // 2- creator
            if ($ticket->created_by == auth()->user()->id) {
                return true;
            }
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'selected' => 'array',
        ];
    }
}
