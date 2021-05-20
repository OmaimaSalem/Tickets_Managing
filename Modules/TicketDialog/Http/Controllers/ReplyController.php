<?php

namespace Modules\TicketDialog\Http\Controllers;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\TicketDialog\Entities\Reply;
use Modules\TicketDialog\Entities\SubReply;
use Modules\TicketDialog\Entities\TicketSubReplyCC;
use Modules\TicketDialog\Http\Requests\CreateReplieRequest;
use Modules\TicketDialog\Http\Requests\CreateSubReplieRequest;
use Modules\TicketDialog\Http\Resources\ReplyResource;
use Modules\TicketDialog\Http\Resources\SubReplyResource;

class ReplyController extends Controller
{

    public function sendResponse($result, $message)

    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index($id)
    {
        $Replies = Reply::where('ticket_id', $id)->latest()->paginate();

        return $this->sendResponse(ReplyResource::collection($Replies), 'Ticket Replies retrieved successfully');

    }

    /**
     * Store a newly created resource in storage.
     * @param $id
     * @param CreateReplieRequest $request
     * @return JsonResponse
     * @throws ItemNotCreatedException
     */
    public function store(CreateReplieRequest $request)
    {
        $input = $request->validated();

        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $ticket = Ticket::find($request->ticket_id);
        if($ticket) {
            $ticket->reply = 1;
            $ticket->save();
        }

        try {
            $ticket_reply = Reply::create($input);

        } catch (Exception $ex) {
            throw new ItemNotCreatedException('reply');
        }

        return $this->sendResponse(new ReplyResource($ticket_reply), 'Reply created successfully.');

    }

    public function storeSub(CreateSubReplieRequest $request){

        $input = $request->validated();


        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        if($request->cc) {
            $mails = explode(',', $request->cc);
            foreach($mails as $mail) {
                $oldMails = TicketSubReplyCC::where('email', '=', $mail)->get();
                if($this->checkSubReplyCC($oldMails, $request->reply_id)) {
                    $saveMail = new TicketSubReplyCC();
                    $saveMail->email = $mail;
                    $saveMail->sub_reply = $request->reply_id;
                    $saveMail->created_at = Carbon::now();
                    $saveMail->created_by =auth()->user()->id;
                    $saveMail->save();
                }
            }
        }
        try {
            $ticket_subReply = SubReply::create($input);

        } catch (Exception $ex) {
            throw new ItemNotCreatedException('sub reply');
        }

        return $this->sendResponse(new SubReplyResource($ticket_subReply), 'Sub Reply created successfully.');

    }

    private function checkSubReplyCC($oldMails, $reply_id) {
        foreach($oldMails as $mail) {
            if($mail->sub_reply == $reply_id) {
                return false;
            }
        }
        return true;
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
}
