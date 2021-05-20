<?php

namespace Modules\TicketDialog\Observers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\TicketDialog\Entities\Comment;
use Modules\TicketDialog\Entities\TicketDialogFiles;
use Modules\TicketDialog\Jobs\TicketCommentJob;

class CommentObserver
{

    private $input;

    public function __construct(Request $request)
    {
        $this->input = $request->all();
    }

    public function created(Comment $TicketComment)
    {
        $ticket = Ticket::find($TicketComment->ticket_id);

        $filesPaths = [];
        if(isset($this->input['files'])){

            $files = $this->input['files'];

            foreach ($files as $file) {

                $fileName = $file->getClientOriginalName();

                $fullFileName = Carbon::now().'-'.$fileName;

                $TicketFiles = new TicketDialogFiles();

                $TicketFiles->create([
                    'file_path' => 'public/tickets/'.$TicketComment->ticket_id.'/ticketDialog/'.$fullFileName,
                    'ticket_id' => $TicketComment->ticket_id,
                ]);

                $file->storeAs('public/tickets/'.$TicketComment->ticket_id.'/ticketDialog/',$fullFileName);
                $path = 'app/public/tickets/'.$TicketComment->ticket_id.'/ticketDialog/'.$fullFileName;

                array_push($filesPaths, ['path' => $path,'name' => $fileName, 'type' => $file->getClientmimeType()]);
            }

        }
        $MailsArr = [];
        $assigns = $ticket->manager()->get();
        foreach ($assigns as $assign){
            if($assign->support === 0) {
                array_push($MailsArr, $assign->email);
            }
        }
        TicketCommentJob::dispatch($TicketComment, $MailsArr, $filesPaths, $ticket);

    }


}
