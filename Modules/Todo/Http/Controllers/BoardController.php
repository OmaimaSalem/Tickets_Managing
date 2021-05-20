<?php

namespace Modules\Todo\Http\Controllers;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;

use Modules\Todo\Http\Resources\BoardCollection;
use Modules\Todo\Http\Resources\BoardResource;
use Carbon\Carbon;

use Modules\Todo\Http\Requests\CreateBoardRequest;
use Modules\Todo\Http\Requests\UpdateBoardRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use  Modules\Todo\Entities\Board;

class BoardController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $boards = Board::orderBy('created_at', 'DESC')->paginate();

        return $this->sendResponse(new BoardCollection($boards), 'Boards retrieved successfully.');
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
    public function store(CreateBoardRequest $request)
    {
      $input = $request->validated();
      $input['created_at'] = Carbon::now();
      //$input['created_by'] = auth()->user()->id;
      $board = Board::create($input);

      return $this->sendResponse(new BoardResource($board), 'Board created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
      $board = Board::find($id);

      return $this->sendResponse(new BoardResource($board), 'Board retrieved successfully.');
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
    public function update(Board $board, UpdateBoardRequest $request)
    {
      $input = $request->validated();
      $board->updated_at = Carbon::now();
      //$board->updated_by = auth()->user()->id;
      $updated = $board->fill($input)->save();

      if (!$updated)
      throw new ItemNotUpdatedException('board');

      return $this->sendResponse(new BoardResource($board), 'Board updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $board = Board::find($id);

        $board->delete();

      return $this->sendResponse(new BoardResource($board), 'Board deleted successfully.');
    }


    public function assignUsers(Request $request) {
      $board = Board::find($request['board_id']['id']);

      foreach($request['users'] as $user) {
        $board->users()->detach($user['id']);
        $board->users()->attach($user['id']);
      }
    }
}
