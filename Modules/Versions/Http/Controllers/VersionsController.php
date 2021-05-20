<?php

namespace Modules\Versions\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\API\BaseController;
use Modules\Versions\Entities\Version;
use Modules\Versions\Http\Requests\StoreRequest;
class VersionsController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

        return $this->sendResponse(['versions' => Version::all()],'Versions retrieved successfully');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        $version = Version::create($request->all());
        return $this->sendResponse(['version' => $version],'Version created successfully');
    }

    
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(StoreRequest $request,Version $version)
    {
        $version->update($request->all());
        return $this->sendResponse(['version' => $version],'Version updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy(Version $version)
    {
        $version->delete();
        return $this->sendResponse(['id' => $version->id],'Version deleted successfully');
    }

    public function upload(Request $request) {
        $file = $request->file('file');
        $path = \Storage::disk('local')->path("chunks\\".$file->getClientOriginalName());
        \File::append($path, $file->get());
        if ($request->has('is_last') && $request->is_last == "true") {
            $name = basename($path, '.part');
            $name = str_replace('chunks\\','',$name);
            $finalpath = \Storage::disk('local')->path('public/versions/'.$name);
            \File::move($path, $finalpath);
            return response()->json(['done' => true,'url' => url('storage/versions/'.$name)]);
        }
        return response()->json(['uploaded' => true]);
    }


    public function download(Request $request,$id) {
       $version = Version::findorfail($id);
       $version->increment("downloads");
       return redirect($version->url);
    }

}
