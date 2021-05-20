<?php

namespace Modules\OfferConversations\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Http\Controllers\API\sendResponse;
use App\Http\Controllers\API\BaseController;
use Modules\OfferConversations\Entities\OfferConversation;
use Modules\OfferConversations\Http\Resources\OfferConversationCollection;

class OfferConversationsController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:offer-list|offer-create|offer-edit|offer-delete', ['only' => ['index', 'show', 'getConversationsPerTicket']]);
        $this->middleware('permission:offer-create', ['only' => ['store']]);
        $this->middleware('permission:offer-edit', ['only' => ['update']]);
        $this->middleware('permission:offer-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $offerConversation = OfferConversation::paginate();
      return $this->sendResponse(new OfferConversationCollection($offerConversation), 'offerConversation retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
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

    public function getConversationsPerOffer($offerId)
    {
        $offerConversations = OfferConversation::where('offer_id', $offerId)->orderBy('created_at', 'DESC')->paginate();
        return $this->sendResponse(new OfferConversationCollection($offerConversations), 'offerConversations retrieved successfully.');
    }

    public function postConversationsPerOffer(Request $request) {
      $offerConversation = new OfferConversation();
      $offerConversation->offer_id = $request->offer_id;
      $offerConversation->created_by = auth()->user()->id;
      $offerConversation->created_at = Carbon::now();
      $offerConversation->conversation = '<div class="card card-primary card-outline">
                                            <div class="card-body p-0">
                                                <div class="mailbox-read-info">
                                                    <h5>' . $request->subject . '</h5>
                                                    <h6>From: ' . auth()->user()->email . '
                                                        <span class="mailbox-read-time float-right">' . Carbon::now()->isoFormat('LLLL') . '</span>
                                                    </h6>
                                                </div>
                                                <div class="mailbox-read-message">
                                                    <p>'. $request->offer_mail.'</p>
                                                </div>
                                            </div>
                                        </div>';
      $offerConversation->save();
    }
}
