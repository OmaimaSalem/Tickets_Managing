<?php

namespace App\Http\Requests\TicketRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;
use App\Exceptions\ItemNotFoundException;

class DeleteTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can delete ticket ??

        // 1- admin
        // if (auth()->user()->isAdmin() || auth()->user()->can('ticket-management')) {
        //     return true;
        // }

        $ticket =$this->route('ticket');
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
