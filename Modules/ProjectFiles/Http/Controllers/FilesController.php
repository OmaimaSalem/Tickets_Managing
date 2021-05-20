<?php

namespace Modules\ProjectFiles\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\File\Http\Resources\FileResource;
use Modules\ProjectFiles\Entities\ProjectFile;
use Modules\ProjectFiles\Http\Requests\uploadFiletRequest;
use Modules\ProjectFiles\Http\Resources\FilesResource;

class FilesController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('projectfiles::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('projectfiles::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(uploadFiletRequest $request)
    {
        $id = $request->project_id;
        $input = $request->validated();
        $files=[];
        foreach ($input['files'] as $file) {
            $input['uploaded_by'] = auth()->user()->id;
            $input['created_at'] = Carbon::now();
            $input['updated_at'] = Carbon::now();
            $input['folder_id'] = null;
            $input['project_id'] = $id;
            $input['type'] = $file->getClientMimeType();
            $input['name'] = $file->getClientOriginalName();
            $fullFileName = Carbon::now().'-'.$input['name'];
            $input['path'] = 'public/projects/'.$input['project_id'].'/'.$fullFileName.'';
//            Storage::disk('local')->put('public/projects/'.$input['project_id'].'/', $file);
            $file->storeAs('public/projects/'.$input['project_id'].'/',$fullFileName);
            $file = ProjectFile::create($input);
            array_push($files, $file);
        }
//        return $this->sendResponse(new FilesResource($files), 'File/s Uploaded successfully.');
        return response()->json(['data'=>$files,'message'=>'File/s Uploaded successfully.']);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $files = Project::find($id)->file->where('folder_id','=',null);
        return $this->sendResponse( FilesResource::collection($files), 'Files retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('projectfiles::edit');
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
        $file = ProjectFile::find($id);
        Storage::delete($file->path);
        $file->delete();
        return $this->sendResponse(new FileResource($file), 'File deleted succesfully');
    }
}
