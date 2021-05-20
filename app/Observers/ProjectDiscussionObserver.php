<?php

namespace App\Observers;

use App\Jobs\Project\ProjectDiscussionsJob;
use App\Models\DiscussionFiles;
use App\Models\ProjectDiscussion;
use App\Mail\ProjectDiscussionMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProjectDiscussionObserver
{
    private $input;

    public function __construct(Request $request)
    {
        $this->input = $request->all();
    }
    /**
     * Handle the project discussion "created" event.
     *
     * @param ProjectDiscussion $projectDiscussion
     * @return void
     */
    public function created(ProjectDiscussion $projectDiscussion)
    {

        $projectDiscussion->title = '##'.$projectDiscussion->id.'##'.$projectDiscussion->title;
        $projectDiscussion->save();


        if ($this->input['responsilbes']) {

            $responsilbes = User::find($this->input['responsilbes']);
            $projectDiscussion->responsibles()->attach($responsilbes);

        }


        $filesPaths = [];

        if(isset($this->input['files'])){

            $files = $this->input['files'];

            foreach ($files as $file) {

                $fileName = $file->getClientOriginalName();

                $dirName = $projectDiscussion->id;

                $fullFileName = Carbon::now().'-'.$fileName;

                $finalPath = $dirName.'/'.$fullFileName;

                $DiscussionFile = new DiscussionFiles();

                $DiscussionFile->create([
                   'file_path' => 'public/projects/'.$projectDiscussion->project_id.'/discussion/'.$finalPath,
                   'discussion_id' => $projectDiscussion->id,
                ]);

                $file->storeAs('public/projects/'.$projectDiscussion->project_id .'/discussion',$finalPath);
                $path = 'app/public/projects/'.$projectDiscussion->project_id .'/discussion/'.$finalPath;

                array_push($filesPaths, ['path' => $path,'name' => $fileName, 'type' => $file->getClientmimeType()]);
            }

        }
            $MailsArr = [];
            foreach ($responsilbes as $responsible){
                array_push($MailsArr, $responsible->email);
            }

            ProjectDiscussionsJob::dispatch($projectDiscussion, $filesPaths, $MailsArr);


    }

    /**
     * Handle the project discussion "updated" event.
     *
     * @param ProjectDiscussion $projectDiscussion
     * @return void
     */
    public function updated(ProjectDiscussion $projectDiscussion)
    {
        //
    }

    /**
     * Handle the project discussion "deleted" event.
     *
     * @param ProjectDiscussion $projectDiscussion
     * @return void
     */
    public function deleted(ProjectDiscussion $projectDiscussion)
    {
        //
    }

    /**
     * Handle the project discussion "restored" event.
     *
     * @param ProjectDiscussion $projectDiscussion
     * @return void
     */
    public function restored(ProjectDiscussion $projectDiscussion)
    {
        //
    }

    /**
     * Handle the project discussion "force deleted" event.
     *
     * @param ProjectDiscussion $projectDiscussion
     * @return void
     */
    public function forceDeleted(ProjectDiscussion $projectDiscussion)
    {
        //
    }
}
