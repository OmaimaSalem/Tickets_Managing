<?php

namespace App\Http\Requests\TicketRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;
use App\Exceptions\ItemNotFoundException;
use Illuminate\Http\Request;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // who can update ticket??

        // 1- admin
        // if (auth()->user()->isAdmin()) {
        //     return true;
        // }

        $ticket_id =$this->route('ticket');
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($ticket_id);

        if($ticket && can_permission('edit','ticket',$ticket->created_by)){
            return true;
        }


        if (!$ticket) {
            throw new ItemNotFoundException($ticket_id);
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
    public function rules(Request $request)
    {
        $rules =  [
            'name' => 'string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|integer|exists:projects,id',
            'status_id' => 'required|integer|exists:status,id',
            'client' => 'nullable',
            'due_date' => '',
            'owner_id' => 'required|array',
            'owner_id.*' => 'required|exists:users,id',
            'manager_id' => 'required|array',
            'manager_id.*' => 'required|exists:users,id',
            'estimated_time' => 'nullable|integer'
        ];
        // if($request->direction){
        //     unset($rules['due_date']);
        //     unset($rules['owner_id']);
        // }

        if($request->kanban || $request->direction){
            $rules =  [
                'name' => 'nullable|string',
                'description' => 'nullable|string',
                'project_id' => 'nullable|integer|exists:projects,id',
                'status_id' => 'nullable|integer|exists:status,id',
                'client' => 'nullable',
                'due_date' => 'nullable',
                'owner_id' => 'nullable|array',
                'owner_id.*' => 'nullable|exists:users,id',
                'manager_id' => 'nullable|array',
                'manager_id.*' => 'nullable|exists:users,id',
                'estimated_time' => 'nullable|integer'
            ];
        }
        return $rules;
    }
}
