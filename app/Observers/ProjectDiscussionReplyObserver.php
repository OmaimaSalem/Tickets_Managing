<?php

namespace App\Observers;

use App\Http\Resources\Project\ProjectDiscussion\ProjectDiscussionRepliesResource;
use App\Http\Resources\Project\ProjectDiscussion\ProjectDiscussionsResource;
use App\Jobs\Project\ProjectDiscussionsJob;
use App\Jobs\Project\ProjectDiscussionsRepliesJob;
use App\Models\DiscussionFiles;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionReply;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProjectDiscussionReplyObserver
{

    private $input;

    public function __construct(Request $request)
    {
        $this->input = $request->all();
    }

    public function created(ProjectDiscussionReply $projectDiscussionReply)
    {

        $discussion = ProjectDiscussion::find($projectDiscussionReply->discussion_id);


        $filesPaths = [];
        if(isset($this->input['files'])){

            $files = $this->input['files'];

            foreach ($files as $file) {

                $fileName = $file->getClientOriginalName();

                $dirName = $discussion->id;

                $fullFileName = Carbon::now().'-'.$fileName;

                $finalPath = $dirName.'/'.$fullFileName;

                $DiscussionFile = new DiscussionFiles();

                $DiscussionFile->create([
                    'file_path' => 'public/projects/'.$discussion->project_id.'/discussion/'.$finalPath,
                    'discussion_id' => $discussion->id,
                ]);

                $file->storeAs('public/projects/'.$discussion->project_id. '/discussion/',$finalPath);
                $path = 'app/public/projects/'.$discussion->project_id .'/discussion/'.$finalPath;

                array_push($filesPaths, ['path' => $path,'name' => $fileName, 'type' => $file->getClientmimeType()]);

            }

        }
        if(isset($this->input['reply'])){

            $oldReplies = [];

            if($discussion->returnOldReplies($projectDiscussionReply->id) && count($discussion->returnOldReplies($projectDiscussionReply->id)) >= 1){

                $oldRepliesObjects = ProjectDiscussionRepliesResource::collection($discussion->returnOldReplies($projectDiscussionReply->id));

                foreach ($oldRepliesObjects as $replyContent){
                    array_push($oldReplies,$replyContent);
                }
            }

            $projectDiscussionReply->reply = 1;
            $projectDiscussionReply->save();

            $MailsArr = [];
            $responsibles = $discussion->responsibles()->get();

            foreach ($responsibles as $responsible){
                array_push($MailsArr, $responsible->email);
            }

            ProjectDiscussionsRepliesJob::dispatch($projectDiscussionReply,$oldReplies, $filesPaths, $MailsArr);


        }

    }

    /**
     * Handle the project discussion reply "updated" event.
     *
     * @param  \App\Models\ProjectDiscussionReply  $projectDiscussionReply
     * @return void
     */
    public function updated(ProjectDiscussionReply $projectDiscussionReply)
    {
        //
    }

    /**
     * Handle the project discussion reply "deleted" event.
     *
     * @param  \App\Models\ProjectDiscussionReply  $projectDiscussionReply
     * @return void
     */
    public function deleted(ProjectDiscussionReply $projectDiscussionReply)
    {
        //
    }

    /**
     * Handle the project discussion reply "restored" event.
     *
     * @param  \App\Models\ProjectDiscussionReply  $projectDiscussionReply
     * @return void
     */
    public function restored(ProjectDiscussionReply $projectDiscussionReply)
    {
        //
    }

    /**
     * Handle the project discussion reply "force deleted" event.
     *
     * @param  \App\Models\ProjectDiscussionReply  $projectDiscussionReply
     * @return void
     */
    public function forceDeleted(ProjectDiscussionReply $projectDiscussionReply)
    {
        //
    }
}
