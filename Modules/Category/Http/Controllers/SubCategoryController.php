<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Category\Entities\SubCategory;
use Modules\Category\Http\Resources\SubCategoriesCollection;
use Modules\Category\Http\Resources\SubCategoriesResource;

class SubCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('category::index');
    }

    public function listAllCategories()
    {
        try {
            $categories = SubCategory::orderBy('created_at', 'asc')->paginate(10);
            return $this->sendResponse(new SubCategoriesCollection($categories), 'Sub Categories retrieved successfully.');
        }catch (\Exception $e){
            return $e->getMessage();
        }


    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('category::create');
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
        return view('category::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('category::edit');
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
}
