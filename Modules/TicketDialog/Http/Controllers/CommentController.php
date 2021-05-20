<?php

namespace Modules\TicketDialog\Http\Controllers;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotUpdatedException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\TicketDialog\Entities\Comment;
use Modules\TicketDialog\Entities\SubComment;
use Modules\TicketDialog\Http\Requests\CreateCommentRequest;
use Modules\TicketDialog\Http\Requests\CreateSubCommentRequest;
use Modules\TicketDialog\Http\Requests\UpdateTicketCommentRequest;
use Modules\TicketDialog\Http\Resources\CommentResource;
use Modules\TicketDialog\Http\Resources\SubCommentResource;

class CommentController extends Controller
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
        $Comments = Comment::where('ticket_id', $id)->latest()->paginate();

        return $this->sendResponse(CommentResource::collection($Comments), 'Ticket comments retrieved successfully');

    }

    /**
     * Store a newly created resource in storage.
     * @param $id
     * @param CreateCommentRequest $request
     * @return JsonResponse
     * @throws ItemNotCreatedException
     */
    public function store(CreateCommentRequest $request)
    {
        $input = $request->validated();

        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;

        try {
            $ticket_comment = Comment::create($input);

        } catch (Exception $ex) {
            throw new ItemNotCreatedException('comment');
        }

        return $this->sendResponse(new CommentResource($ticket_comment), 'Comment created successfully.');

    }

    public function storeSub(CreateSubCommentRequest $request){
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        try {
            $ticket_SubComment = SubComment::create($input);

        } catch (Exception $ex) {
            throw new ItemNotCreatedException('sub comment');
        }


        return $this->sendResponse(new SubCommentResource($ticket_SubComment), 'Comment created successfully.');

    }

    public function update(UpdateTicketCommentRequest $request)
    {
        $input = $request->validated();

        $Comment = Comment::find($request->id);

        $Comment->updated_at = Carbon::now();

        try {
            $Comment->update($input);
            $Comment->save();
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            throw new ItemNotUpdatedException('ticket Comment');
        }

        if (!$Comment)
            throw new ItemNotUpdatedException('ticket Comment');

        return $this->sendResponse(new CommentResource($Comment), 'ticket Comment updated successfully.');
    }

    public function SubUpdate(UpdateTicketCommentRequest $request)
    {

        $input = $request->validated();
        $SubComment = SubComment::find($request->id);

        $SubComment->updated_at = Carbon::now();

        try {
            $updated = $SubComment->fill($input)->save();
        } catch (\Exception $ex) {
            throw new ItemNotUpdatedException('ticket sub Comment');
        }

        if (!$updated)
            throw new ItemNotUpdatedException('ticket sub Comment');

        return $this->sendResponse(new SubCommentResource($SubComment), 'ticket Sub Comment updated successfully.');
    }

    public function destroy($id)
    {
        $Comment = Comment::find($id);
        try {
            $Comment->delete();
        } catch (\Exception $ex) {
            throw new ItemNotDeletedException('Comment');
        }

        return $this->sendResponse(true,'ticket Comment deleted successfully.');
    }

    public function SubDestroy($id)
    {
        $SubComment = SubComment::find($id);
        try {
            $SubComment->delete();
        } catch (\Exception $ex) {
            throw new ItemNotDeletedException('Sub Comment');
        }

        return $this->sendResponse(true,'ticket Sub Comment deleted successfully.');
    }



    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */

}
