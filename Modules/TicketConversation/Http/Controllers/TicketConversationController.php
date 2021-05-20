<?php

namespace Modules\TicketConversation\Http\Controllers;

use App\Http\Controllers\API\sendResponse;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\TicketConversation\Entities\TicketConversation;
use Modules\TicketConversation\Http\Resources\TicketConversationCollection;

class TicketConversationController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:ticket-list|ticket-create|ticket-edit|ticket-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:ticket-create', ['only' => ['store']]);
        $this->middleware('permission:ticket-edit', ['only' => ['update']]);
        $this->middleware('permission:ticket-delete', ['only' => ['destroy']]);
        $this->middleware('permission:ticket-list|ticket-management,', ['only' => ['getConversationsPerTicket']]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $TicketConversation = TicketConversation::paginate();
        return $this->sendResponse(new TicketConversationCollection($TicketConversation), 'ticketConversation retrieved successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function getConversationsPerTicket($ticketId)
    {
        $ticketConversations = TicketConversation::where('ticket_id', $ticketId)->orderBy('created_at', 'DESC')->paginate(2);
        return $this->sendResponse(new TicketConversationCollection($ticketConversations), 'ticketConversations retrieved successfully.');
    }
}
