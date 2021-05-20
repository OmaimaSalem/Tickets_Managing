<?php

namespace Modules\TicketComment\Http\Controllers;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\TicketComment\Entities\TicketComment;
use Modules\TicketComment\Http\Requests\AddTicketCommentRequest;
use Modules\TicketComment\Http\Requests\DeleteTicketCommentRequest;
use Modules\TicketComment\Http\Requests\UpdateTicketCommentRequest;
use Modules\TicketComment\Http\Resources\TicketCommentCollection;
use Modules\TicketComment\Http\Resources\TicketCommentResource;
use Modules\TicketConversation\Entities\TicketConversation;

class TicketCommentController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:ticketComment-list|ticketComment-create|ticketComment-edit|ticketComment-delete|ticket-management', ['only' => ['index', 'show', 'getCommentsPerTicket']]);
        $this->middleware('permission:ticketComment-create|ticket-management', ['only' => ['store']]);
        $this->middleware('permission:ticketComment-edit|ticket-management', ['only' => ['update']]);
        $this->middleware('permission:ticketComment-delete|ticket-management', ['only' => ['destroy']]);
    }
     /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $ticketComments = TicketComment::paginate();

        return $this->sendResponse(new TicketCommentCollection($ticketComments), 'ticketCommenties retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param AddTicketCommentRequest $request
     * @return Response
     */
    public function store(AddTicketCommentRequest $request)
    {
        $input = $request->validated();

        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;

        try {
          $ticketComment = TicketComment::create($input);
          if($input['reply']) {
            $ticket = $ticketComment->ticket;
            $ticketConversation = new TicketConversation();
            $ticketConversation->ticket_id = $ticket->id;
            $ticketConversation->created_by = auth()->user()->id;
            $ticketConversation->conversation = '<div class="card card-primary card-outline">
                                                  <div class="card-body p-0">
                                                      <div class="mailbox-read-info">
                                                          <h5>' . $input['subject'] . '</h5>
                                                          <h6>From: ' . $ticketComment->creator->email . '
                                                              <span class="mailbox-read-time float-right">' . Carbon::now()->isoFormat('LLLL') . '</span>
                                                          </h6>
                                                      </div>
                                                      <div class="mailbox-read-message">
                                                          <p>'. $input['mail'].'</p>
                                                      </div>
                                                  </div>
                                              </div>';
            $ticketConversation->save();
            $ticket->reply = 1;
          } else if(auth()->user()->type == "regular-user") {
            return $this->sendResponse(new TicketCommentResource($ticketComment), 'ticketComment created successfully.');
          } else {
            $ticket = $ticketComment->ticket;
            $ticketConversation = new TicketConversation();
            $ticketConversation->ticket_id = $ticket->id;
            $ticketConversation->created_by = auth()->user->id;
            $ticketConversation->conversation = '<div class="card card-primary card-outline">
                                                  <div class="card-body p-0">
                                                      <div class="mailbox-read-info">
                                                          <h5>' . $input['subject'] . '</h5>
                                                          <h6>From: ' . $ticketComment->creator->email . '
                                                              <span class="mailbox-read-time float-right">' . Carbon::now()->isoFormat('LLLL') . '</span>
                                                          </h6>
                                                      </div>
                                                      <div class="mailbox-read-message">
                                                          <p>'. $input['mail'].'</p>
                                                      </div>
                                                  </div>
                                              </div>';
            $ticketConversation->save();
            $ticket->reply = 0;
          }
            $ticket->save();

        } catch (Exception $ex) {
          throw new ItemNotCreatedException('ticketComment');
        }

        return $this->sendResponse(new TicketCommentResource($ticketComment), 'ticketComment created successfully.');
    }

    /**
     * Show the specified resource.
     * @param TicketComment $ticketComment
     * @return Response
     */
    public function show(TicketComment $ticketComment)
    {
        return $this->sendResponse(new TicketCommentResource($ticketComment), 'ticketComment retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     * @param TicketComment $ticketComment
     * @param UpdateTicketCommentRequest $request
     * @return Response
     */
    public function update(TicketComment $ticketComment, UpdateTicketCommentRequest $request)
    {
        $input = $request->validated();

        $ticketComment->updated_at = Carbon::now();
        $ticketComment->updated_by = auth()->user()->id;

        try {
            $updated = $ticketComment->fill($input)->save();
        } catch (\Exception $ex) {
            throw new ItemNotUpdatedException('ticketComment');
        }

        if (!$updated)
        throw new ItemNotUpdatedException('ticketComment');

        return $this->sendResponse(new TicketCommentResource($ticketComment), 'ticketComment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param TicketComment $ticketComment
     * @param DeleteTicketCommentRequest $request
     * @return Response
     */
    public function destroy(TicketComment $ticketComment, DeleteTicketCommentRequest $request)
    {
        try {
            $ticketComment->delete();
        } catch (\Exception $ex) {
            throw new ItemNotDeletedException('ticketComment');
        }

        return $this->sendResponse(new TicketCommentResource($ticketComment), 'ticketComment deleted successfully.');
    }

    /**
     * Display a listing of the resource by TicketId
     * @param $ticketId
     * @return Response
     */
    public function getCommentsPerTicket($ticketId)
    {
        if(auth()->user()->type == 'client') {
            $ticketComments = TicketComment::where('ticket_id', $ticketId)
                              ->where('by_client', '=', '1')
                              ->paginate();
            return $this->sendResponse(new TicketCommentCollection($ticketComments), 'ticketCommenties retrieved successfully.');
        } else {
            $ticketComments = TicketComment::where('ticket_id', $ticketId)
                              ->paginate();
            return $this->sendResponse(new TicketCommentCollection($ticketComments), 'ticketCommenties retrieved successfully.');
        }

    }
}
