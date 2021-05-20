<?php

namespace Modules\ItemCategory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Modules\ItemCategory\Entities\ItemCategory;

use Modules\ItemCategory\Http\Requests\AddItemCategoyRequest;
use Modules\ItemCategory\Http\Requests\UpdateItemCategoryRequest;
use Modules\ItemCategory\Http\Requests\DeleteItemCategoryRequest;

use Modules\ItemCategory\Http\Resources\ItemCategoryResource;
use Modules\ItemCategory\Http\Resources\ItemCategoryCollection;

use App\Http\Controllers\API\BaseController;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\ItemNotDeletedException;
use Carbon\Carbon;

class ItemCategoryController extends BaseController
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
      $categories = ItemCategory::paginate();
      return $this->sendResponse(new ItemCategoryCollection($categories), 'Categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(AddItemCategoyRequest $request)
    {
      $input = $request->validated();
      // dd($request->name);
      $input['created_at'] = Carbon::now();
      $input['created_by'] = auth()->user()->id;
      try {
        $category = ItemCategory::create($input);
      } catch (Exception $ex) {
        throw new ItemNotCreatedException('Category');
      }


      return $this->sendResponse(new ItemCategoryResource($category), 'Category created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show(ItemCategory $category)
    {
      return $this->sendResponse(new ItemCategoryResource($category), 'Category retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(ItemCategory $category, UpdateItemCategoryRequest $request)
    {
      $input = $request->validated();
      $category->updated_at = Carbon::now();
      $category->updated_by = auth()->user()->id;

      try {
          $updated = $category->fill($input)->save();
      } catch (\Exception $ex) {
          throw new ItemNotUpdatedException('Category');
      }

      if (!$updated)
      throw new ItemNotUpdatedException('Category');

      return $this->sendResponse(new ItemCategoryResource($category), 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $category = ItemCategory::find($id);
      if($category->items->isNotEmpty()) {
          throw new InvalidDataException([
              'items' => $category->items->toArray()
          ],
          'Can\'t delete!, Category has items.');
      }

      try {
          $category->delete();
      } catch (\Exception $ex) {
          throw new ItemNotDeletedException('Category');
      }

        return $this->sendResponse(new ItemCategoryResource($category), 'Category deleted successfully.');
    }
}
