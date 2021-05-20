<?php

namespace Modules\Todo\Http\Controllers;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;

use Modules\Todo\Http\Resources\CardCollection;
use Modules\Todo\Http\Resources\CardResource;
use Carbon\Carbon;

use Modules\Todo\Http\Requests\CreateCardRequest;
use Modules\Todo\Http\Requests\UpdateCardRequest;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use  Modules\Todo\Entities\Card;
use  Modules\Todo\Entities\Task;
use  Modules\Todo\Entities\Board;
use  App\Models\User;




class CardController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $cards = Card::orderBy('id', 'DESC')->paginate();
      return $this->sendResponse(new CardCollection($cards), 'Cards retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('todo::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(CreateCardRequest $request)
    {
      $input = $request->validated();
      $input['created_at'] = Carbon::now();
      //$input['created_by'] = auth()->user()->id;
      $card = Card::create($input);

      return $this->sendResponse(new CardResource($card), 'Card created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
      $card = Card::find($id);

      return $this->sendResponse(new CardResource($card), 'Card retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('todo::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Card $card, UpdateCardRequest $request)
    {

      $input = $request->validated();
      $card->updated_at = Carbon::now();
      $updated = $card->fill($input)->save();
      if (!$updated)
      throw new ItemNotUpdatedException('card');

      return $this->sendResponse(new CardResource($card), 'Card updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $card = Card::find($id);
      if(count($card->tasks) > 0 )
      {
        foreach ($card->tasks as $task ) {
          $task = Task::find($task->id);
          $task->delete();
        }
      }
      $card->delete();

      return $this->sendResponse(new CardResource($card), 'Card deleted successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function getCardsByBoardId($id)
    {
      $cards = Board::find($id)->cards;
      return $this->sendResponse( CardResource::collection($cards), 'Card Retived successfully.');
    }
}
