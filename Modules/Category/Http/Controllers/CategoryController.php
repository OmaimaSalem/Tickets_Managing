<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Modules\Category\Entities\Category;

use Modules\Category\Http\Requests\AddCategoryRequest;
use Modules\Category\Http\Requests\UpdateCategoryRequest;
use Modules\Category\Http\Requests\DeleteCategoryRequest;

use Modules\Category\Http\Resources\CategoryResource;
use Modules\Category\Http\Resources\CategoryCollection;

use App\Http\Controllers\API\BaseController;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\ItemNotDeletedException;
use Carbon\Carbon;

class CategoryController extends BaseController
{

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('permission:category-list', ['only' => ['index', 'show']]);
      $this->middleware('permission:category-create', ['only' => ['store']]);
      $this->middleware('permission:category-edit', ['only' => ['update']]);
      $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $categories = Category::query();

        if($request->input('query')) {
            $categories->where('name', 'like', '%' . $request->input('query') . '%');
        }

        if(!auth()->user()->hasRole('Full Administrator') && !auth()->user()->can('category-list')) {
            $categories->whereHas('allowed_users', function($query) {
                $query->where('user_id', auth()->user()->id);
            });
        }

        $categories =  $categories->paginate();
        return $this->sendResponse(new CategoryCollection($categories), 'Categories retrieved successfully.');
    }

    public function listAllCategories()
    {
        $categories = Category::all('id', 'name');
        return $this->sendResponse($categories, 'Categories retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param AddCategoryRequest $request
     * @return Response
     */
    public function store(AddCategoryRequest $request)
    {
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        try {
          $category = Category::create($input);
        } catch (Exception $ex) {
          throw new ItemNotCreatedException('Category');
        }

        if($request->allowed_users)
        {
            $category->allowed_users()->sync($request->allowed_users);
        }



        return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
    }

    /**
     * Show the specified resource.
     * @param Category $category
     * @return Response
     */
    public function show(Category $category)
    {

        if(!auth()->user()->hasRole('Full Administrator')  && !auth()->user()->can('category-list')) {
            $category = auth()->user()->allowed_categories()->find($category->id);
            if(!$category) {
                return $this->sendError([], 'this action not allowed.');
            }
        }


        return $this->sendResponse(new CategoryResource($category->load('topics:id,name,category_id','allowed_users:users.id,name')), 'Category retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     * @param Category $category
     * @param UpdateCategoryRequest $request
     * @return Response
     */
    public function update(Category $category, UpdateCategoryRequest $request)
    {

        $input = $request->validated();
        $category->updated_at = Carbon::now();
        $category->updated_by = auth()->user()->id;

        try {
            $updated = $category->fill($input)->save();
        } catch (\Exception $ex) {
            throw new ItemNotUpdatedException('Category');
        }

        if($request->allowed_users)
        {
            $category->allowed_users()->sync($request->allowed_users);
        }

        if (!$updated)
        throw new ItemNotUpdatedException('Category');

        return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param Category $category
     * @param DeleteCategoryRequest $request
     * @return Response
     */
    public function destroy(Category $category, DeleteCategoryRequest $request)
    {
        if($category->topics && $category->topics->isNotEmpty()) {
            throw new InvalidDataException([
                'topics' => $category->topics->toArray()
            ],
            'Can\'t delete!, Category has topics.');
        }
        if($category->items && $category->items->isNotEmpty()) {
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

          return $this->sendResponse(new CategoryResource($category), 'Category deleted successfully.');
    }
}
