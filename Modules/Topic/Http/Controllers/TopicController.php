<?php

namespace Modules\Topic\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Topic\Entities\Topic;

use Modules\Topic\Http\Requests\AddTopicRequest;
use Modules\Topic\Http\Requests\UpdateTopicRequest;
use Modules\Topic\Http\Requests\DeleteTopicRequest;

use Modules\Topic\Http\Resources\TopicResource;
use Modules\Topic\Http\Resources\TopicCollection;

use App\Http\Controllers\API\BaseController;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\ItemNotDeletedException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class TopicController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $topics = Topic::query();

        if ($request->input('query')) {
            $topics->where('name', 'like', '%' . $request->input('query') . '%');
        }

        if(!auth()->user()->hasRole('Full Administrator') && !auth()->user()->can('wiki-list')) {
            $topics->whereHas('category.allowed_users', function($topic) {
                $topic->where('user_id', auth()->user()->id);
            });
        }

        $topics =  $topics->paginate();
        return $this->sendResponse(new TopicCollection($topics), 'Topics retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param AddTopicRequest $request
     * @return Response
     */
    public function store(AddTopicRequest $request)
    {
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;

        try {
            $topic = Topic::create($input);
        } catch (Exception $ex) {
            throw new ItemNotCreatedException('Topic');
        }

        return $this->sendResponse(new TopicResource($topic), 'Topic created successfully.');
    }

    /**
     * Show the specified resource.
     * @param Topic $topic
     * @return Response
     */
    public function show(Topic $topic)
    {
        if(!$topic->allowed() && !auth()->user()->hasRole('Full Administrator') && !auth()->user()->can('wiki-list') ){
           return $this->sendError([], 'Action not allowed.');
        }
        return $this->sendResponse(new TopicResource($topic->load('category')), 'Topic retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     * @param Topic $topic
     * @param UpdateTopicRequest $request
     * @return Response
     */
    public function update(Topic $topic, UpdateTopicRequest $request)
    {
        if(!$topic->allowed()){
           return $this->sendError([], 'Action not allowed.');
        }
        $input = $request->validated();

        $topic->updated_at = Carbon::now();
        $topic->updated_by = auth()->user()->id;

        try {
            $updated = $topic->fill($input)->save();
        } catch (\Exception $ex) {
            throw new ItemNotUpdatedException('Topic');
        }

        if (!$updated)
            throw new ItemNotUpdatedException('Topic');

        return $this->sendResponse(new TopicResource($topic), 'Topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param Topic $topic
     * @param DeleteTopicRequest $request
     * @return Response
     */
    public function destroy(Topic $topic, DeleteTopicRequest $request)
    {
        if(!$topic->allowed()){
            return $this->sendError([], 'Action not allowed.');
        }
        try {
            $topic->delete();
        } catch (\Exception $ex) {
            throw new ItemNotDeletedException('Topic');
        }

        return $this->sendResponse(new TopicResource($topic), 'Topic deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $allowedfileExtension = ['jpg', 'png'];
        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $check = in_array($extension, $allowedfileExtension);
        if ($check) {
            $filename  = 'wiki-' . time() . '.' . $extension;
            $path = 'public/wiki/'.date("Y/m").'/';
            $file->storeAs($path, $filename);
            $imageLink = Storage::url('wiki/'.date("Y/m").'/'. $filename);
            return response()->json($imageLink);
        } else {
            return response()->json(['invalid_file_format'], 422);
        }
    }
}
