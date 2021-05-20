<?php

namespace Modules\MailTemplate\Http\Controllers;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\ItemNotFoundException;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\sendResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MailTemplate\Entities\MailTemplateCategory;
use Modules\MailTemplate\Http\Requests\AddCategoryRequest;
use Modules\MailTemplate\Http\Requests\DeleteCategoryRequest;
use Modules\MailTemplate\Http\Requests\UpdateCategoryRequest;
use Modules\MailTemplate\Http\Resources\CategoryCollection;
use Modules\MailTemplate\Http\Resources\CategoryResource;

class MailTemplateCategoryController extends BaseController
{

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('permission:mail-template');
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $categories = MailTemplateCategory::paginate();

      return $this->sendResponse(new CategoryCollection($categories), 'Categories retrieved successfully.');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(AddCategoryRequest $request)
    {
      $input = $request->validated();
      $input['created_at'] = Carbon::now();
      $input['created_by'] = auth()->user()->id;
      try {
        $category = MailTemplateCategory::create($input);
      } catch (Exception $ex) {
        throw new ItemNotCreatedException('Category');
      }

      return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
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
    public function update(UpdateCategoryRequest $request, $id)
    {
      $input = $request->validated();
      $category = MailTemplateCategory::find($id);
      if(!$category) {
        throw new ItemNotFoundException('Category');
      }
      $category->updated_at = Carbon::now();
      $category->updated_by = auth()->user()->id;

      try {
          $updated = $category->fill($input)->save();
      } catch (\Exception $ex) {
          throw new ItemNotUpdatedException('Category');
      }

      if (!$updated)
      throw new ItemNotUpdatedException('Category');

      return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $category = MailTemplateCategory::find($id);
      if($category->templates->isNotEmpty()) {
          throw new InvalidDataException([
              'templates' => $category->templates->toArray()
          ],
          'Can\'t delete!, Category has templates.');
      }

      try {
          $category->delete();
      } catch (\Exception $ex) {
          throw new ItemNotDeletedException('Category');
      }

        return $this->sendResponse(new CategoryResource($category), 'Category deleted successfully.');
    }
}
