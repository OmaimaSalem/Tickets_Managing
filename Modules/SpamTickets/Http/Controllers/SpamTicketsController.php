<?php

namespace Modules\SpamTickets\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\SpamTickets\Entities\SpamTickets;
use Modules\SpamTickets\Http\Resources\SpamTicketsCollection;
use Modules\SpamTickets\Http\Resources\SpamTicketsResource;
use App\Notifications\Admin\Ticket\SpamTicketNotification;
class SpamTicketsController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $tickets = SpamTickets::latest()->paginate();
        return $this->sendResponse(new SpamTicketsCollection($tickets), 'Spam tickets retrived successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (count($request->selected) > 1) {
            foreach($request->selected as $ticket_id) {
                $ticket = Ticket::find($ticket_id);
                $clients = $ticket->owner;
                foreach($clients as $client) {
                    $client->spam = 1;
                    $client->save();
                }
                $spamTicket = new SpamTickets();
                $spamTicket->name = $ticket->name;
                $spamTicket->description = $ticket->description;
                $spamTicket->read = $ticket->read;
                $spamTicket->created_by = $ticket->created_by;
                $spamTicket->owner_id = $ticket->owner[0]->id;
                $spamTicket->save();
                \Notification::send(getRoleUsers('Full Administrator'),new SpamTicketNotification($spamTicket,auth()->user()));

            $ticket->delete();
            }

            return $this->sendResponse(null, 'Succssefuly add tickets to spam.');
        } elseif (count($request->selected) == 1) {
            $ticket = Ticket::find($request->selected[0]);
            $clients = $ticket->owner;
            foreach($clients as $client) {
                $client->spam = 1;
                $client->save();
            }
            $spamTicket = new SpamTickets();
            $spamTicket->name = $ticket->name;
            $spamTicket->description = $ticket->description;
            $spamTicket->read = $ticket->read;
            $spamTicket->created_by = $ticket->created_by;
            $spamTicket->owner_id = $ticket->owner[0]->id;
            $spamTicket->save();
            $ticket->delete();

            \Notification::send(getRoleUsers('Full Administrator'),new SpamTicketNotification($spamTicket,auth()->user()));

            return $this->sendResponse(new SpamTicketsResource($ticket), 'Succssefuly add ticket to spam.');
        }


    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('spamtickets::show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request)
    {
        foreach ($request->all() as $id){
            $ticket = SpamTickets::find($id);
            $ticket->delete();
        }

        return $this->sendResponse(null, 'Succssefuly deleted ticket to spam.');
    }
}
