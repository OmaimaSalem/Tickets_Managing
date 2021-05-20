<?php

namespace App\Http\Requests\TicketRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;
use App\Exceptions\ItemNotFoundException;

class ViewTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can view ticket ??

        // 1- admin
        if (auth()->user()->isAdmin() || auth()->user()->can('other-ticket-list') ) {
            return true;
        }

        $ticket_id = $this->route('ticket');
        $ticket    = Ticket::withoutGlobalScopes(['collection'])->find($ticket_id);


        if(can_permission('delete','ticket',$ticket->created_by)){
            return true;
        }


        if (!$ticket) {
            throw new ItemNotFoundException($ticket_id);
        }

        // 2- creator
        if ($ticket->created_by == auth()->user()->id) {
            return true;
        }
        // 3- project owner
        if($ticket->project) {
            // 2- project owner
            if($ticket->project->owner->count() > 0) {
                foreach ($ticket->project->owner as $owner) {
                    if ($owner->id == auth()->user()->id) {
                        return true;
                    }
                }
            }
        }

        // 4- assigns
        if($ticket->project->assigns->count() > 0) {
            foreach ($ticket->project->assigns as $assign) {
                if ($assign->id == auth()->user()->id) {
                    return true;
                }
            }
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
