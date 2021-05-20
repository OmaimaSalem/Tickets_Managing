<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Http\Requests\ProjectRequest\ProjectDiscussionRequests\CreateDiscussionRequest;
use App\Http\Requests\ProjectRequest\ProjectDiscussionRequests\ReplyToDiscussionRequest;
use App\Http\Resources\Project\ProjectDiscussion\ProjectDiscussionRepliesResource;
use App\Http\Resources\Project\ProjectDiscussion\ProjectDiscussionsResource;
use App\Mail\ProjectDiscussionMail;
use App\Models\Project;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionReply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class ProjectDiscussionController extends Controller
{
    public function sendResponse($result, $message)

    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    public function index($project_id){

        $ProjectDiscussion = new ProjectDiscussion();

            if ($ProjectDiscussion->isManager(auth()->user()->id , $project_id) || auth()->user()->isAdmin()){

                $Discussions = ProjectDiscussion::where('project_id', $project_id)->latest()->paginate();

                return $this->sendResponse(ProjectDiscussionsResource::collection($Discussions),'Project discussions retrieved successfully');
            } else{

            return response()->json('This is a private discussion , Only project Managers could see this discussion', 400);

        }

    }


    public function store($project_id , CreateDiscussionRequest $request){

        $ProjectDiscussion = new ProjectDiscussion();


        if($ProjectDiscussion->isManager(auth()->user()->id , $project_id )|| auth()->user()->isAdmin()){

            $input = $request->validated();

            $input['created_at'] = Carbon::now();
            $input['created_by'] = auth()->user()->id;
            $input['project_id'] = $project_id;
            $input['comments_num'] = 0;

            try {
                $Discussion = ProjectDiscussion::create($input);

            } catch (Exception $ex) {
                throw new ItemNotCreatedException('discussion');
            }

            return $this->sendResponse(new ProjectDiscussionsResource($Discussion), 'discussion created successfully.');

        }else{

             return response()->json('This is a private discussion , Only project Managers could see this discussion', 400);

        }
    }


    public function test(){
        $data = "ss";
        \Mail::to(['mansourtony44@gmail.com'])
            ->send(new ProjectDiscussionMail($data));
        return response()->json(['done']);
    }

    public function addClients($id,Request $request){

        $clients = $request->all();

        $Discussion = ProjectDiscussion::find($id);

        $Discussion->responsibles()->sync($clients);

        return $this->sendResponse(true,'discussion updated successfully.');

    }

    public function deleteClient($id , Request $request){

        $Discussion = ProjectDiscussion::find($id);

        $client = User::find($request['client_id']);

        $Discussion->responsibles()->detach($client);

        return $this->sendResponse(new ProjectDiscussionsResource($Discussion),'client deleted from discussion successfully.');

    }

    public function show($id){

        $Discussion = ProjectDiscussion::find($id);

        $Discussion->seen = 1;
        $Discussion->save();

        return $this->sendResponse(new ProjectDiscussionsResource($Discussion), 'discussion retrieved successfully');

    }

    public function reply(ReplyToDiscussionRequest $request, $id){

        $ProjectDiscussion = ProjectDiscussion::find($id);
        $input = $request->validated();

        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $input['discussion_id'] = $id;
        $input['title'] = '##'.$id.'##'.$request->title;

        try {
            $reply = ProjectDiscussionReply::create($input);

            $ProjectDiscussion->addReply($id);

        } catch (Exception $ex) {
            throw new ItemNotCreatedException('reply');
        }

        return $this->sendResponse(new ProjectDiscussionRepliesResource($reply), 'reply posted successfully.');

    }

    public function destroy($id){

        try {
            $ids = explode(",", $id);
            ProjectDiscussion::find($ids)->each(function ($discussion) {
                $discussion->delete();
                $discussion->responsibles()->detach();
            });

        } catch (\Exception $ex) {
            throw new ItemNotDeletedException('discussion');
        }

        return $this->sendResponse( true,'discussion deleted successfully');

    }

}
