<?php

namespace Modules\Item\Http\Controllers;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\sendResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Item\Entities\Item;
use Modules\Item\Http\Requests\AddItemRequest;
use Modules\Item\Http\Requests\DeleteItemRequest;
use Modules\Item\Http\Requests\UpdateItemRequest;
use Modules\Item\Http\Resources\ItemsCollection;
use Modules\Item\Http\Resources\ItemsResource;

class ItemController extends BaseController
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('permission:item-list', ['only' => ['index', 'show']]);
      $this->middleware('permission:item-create', ['only' => ['store']]);
      $this->middleware('permission:item-edit', ['only' => ['update']]);
      $this->middleware('permission:item-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $items = Item::paginate();

        return $this->sendResponse(new ItemsCollection($items), 'Items retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(AddItemRequest $request)
    {
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
    
        try {
          $item = Item::create($input);
        } catch (Exception $ex) {
          throw new ItemNotCreatedException('Item');
        }
    
        return $this->sendResponse(new ItemsResource($item), 'Item created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(Item $item)
    {
        return $this->sendResponse(new ItemsResource($item), 'Item retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Item $item, UpdateItemRequest $request)
    {
        $input = $request->validated();

        $item->updated_at = Carbon::now();
        $item->updated_by = auth()->user()->id;

        try {
            $updated = $item->fill($input)->save();
        } catch (\Exception $ex) {
            throw new ItemNotUpdatedException('Item');
        }    

        if (!$updated)
        throw new ItemNotUpdatedException('Item');

        return $this->sendResponse(new ItemsResource($item), 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Item $item, DeleteItemRequest $request)
    {
        try {
            $item->delete();
        } catch (\Exception $ex) {
            throw new ItemNotDeletedException('Item');
        }
      
          return $this->sendResponse(new ItemsResource($item), 'Item deleted successfully.');
    }
}
