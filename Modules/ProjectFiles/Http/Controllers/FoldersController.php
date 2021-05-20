<?php

namespace Modules\ProjectFiles\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\File\Http\Resources\FileResource;
use Modules\ProjectFiles\Entities\ProjectFile;
use Modules\ProjectFiles\Entities\ProjectFolder;
use Modules\ProjectFiles\Http\Requests\CreateFolderRequest;
use Modules\ProjectFiles\Http\Requests\uploadFileToFolderRequest;
use Modules\ProjectFiles\Http\Resources\FolderCollection;
use Modules\ProjectFiles\Http\Resources\FolderResource;
use Modules\Todo\Entities\Card;
use Webklex\IMAP\Folder;

class FoldersController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

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
     * @return Response
     */
    public function store(CreateFolderRequest $request)
    {
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['updated_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $folder_path = 'public/projects/'.$input['project_id'].'/'.$input['name'].'';
        $input['path'] = $folder_path;
        Storage::disk('local')->makeDirectory($folder_path);
        $folder = ProjectFolder::create($input);

        return $this->sendResponse(new FolderResource($folder), 'Folder created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $folders = Project::find($id)->folder;
        return $this->sendResponse( FolderResource::collection($folders), 'Folders retrieved successfully.');
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
        $folder = ProjectFolder::find($id);
        $path = $folder->path;
        $final_path = str_replace($folder->name, $request->name, $path);
        if ($path != $final_path){
            Storage::rename($folder->path, $final_path);
        }
        $folder->name = $request->name;
        $folder->description = $request->description;
        $folder->path = $final_path;
        $folder->updated_at = Carbon::now();
        $folder->save();
        return $this->sendResponse(new FolderResource($folder), 'Folder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $folder = ProjectFolder::find($id);
        Storage::delete($folder->path);
        $folder->delete();
        return $this->sendResponse(new FolderResource($folder), 'Folder deleted successfully');
    }
    public function getSingleFolder($id)
    {
        $folder = ProjectFolder::find($id)->path;
        $files = Storage::files($folder);
        $result=[];
        foreach ( $files as $path) {
            $file = ProjectFile::where('path', '=',$path)->first();
            $file->uploaded_by = User::find($file->uploaded_by)->only(['id','name']);
            array_push($result, $file);
        }
        return response()->json(['data'=>$result,'message'=>'File/s Uploaded successfully.']);
    }
    public function uploadInFolder(uploadFileToFolderRequest $request)
    {
        $project_id = $request->project_id;
        $folder_id = $request->folder_id;
        $input = $request->validated();
        $files=[];
        foreach ($input['files'] as $file) {
            $input['uploaded_by'] = auth()->user()->id;
            $input['created_at'] = Carbon::now();
            $input['updated_at'] = Carbon::now();
            $input['folder_id'] = $folder_id;
            $folder_name = ProjectFolder::find($folder_id)->name;
            $input['project_id'] = $project_id;
            $input['type'] = $file->getClientMimeType();
            $input['name'] = $file->getClientOriginalName();
            $fullFileName = Carbon::now().'-'.$input['name'];
            $input['path'] = 'public/projects/'.$input['project_id'].'/'.$folder_name.'/'.$fullFileName.'';
//            Storage::disk('local')->put('public/projects/'.$input['project_id'].'/', $file);
            $file->storeAs('public/projects/'.$input['project_id'].'/'.$folder_name.'/',$fullFileName);
            $file = ProjectFile::create($input);
            $file->uploaded_by = User::find($file->uploaded_by)->only(['id','name']);
            array_push($files, $file);
        }
//        return $this->sendResponse(new FilesResource($files), 'File/s Uploaded successfully.');
        return response()->json(['data'=>$files,'message'=>'File/s Uploaded successfully.']);
    }
    public function copyToFolder(Request $request)
    {
        $selected_files = $request->selected_files;
        $destination = ProjectFolder::find($request->destination);
        foreach ($selected_files as $file){
            $file = ProjectFile::find($file);
            $copied_file = $file->replicate();
            $copied_file->uploaded_by = auth()->user()->id;
            $copied_file->created_at = Carbon::now();
            $copied_file->updated_at = Carbon::now();
            $copied_file->folder_id = $destination->id;
            $fullPath = $destination->path.'/'.Carbon::now().'-'.$file->name;
            $copied_file->path = $fullPath;
            $copied_file->save();
            Storage::copy($file->path, $fullPath);
        }
        return response()->json(['message'=>'File/s Copied successfully.']);
    }
    public function moveToFolder(Request $request)
    {
        $selected_files = $request->selected_files;
        $destination = ProjectFolder::find($request->destination);
        foreach ($selected_files as $file){
            $file = ProjectFile::find($file);
            $file->updated_at = Carbon::now();
            $file->folder_id = $destination->id;
            $fullPath = $destination->path.'/'.Carbon::now().'-'.$file->name;
            $old_path = $file->path;
            $file->path = $fullPath;
            $file->save();
            Storage::move($old_path, $fullPath);
        }
        return response()->json(['message'=>'File/s Moved successfully.']);
    }
}
