<?php

namespace Modules\TicketDialog\Observers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\TicketDialog\Entities\Reply;
use Modules\TicketDialog\Entities\SubReply;
use Modules\TicketDialog\Entities\TicketDialogFiles;
use Modules\TicketDialog\Http\Resources\ReplyResource;
use Modules\TicketDialog\Http\Resources\SubReplyResource;
use Modules\TicketDialog\Jobs\TicketReplyJob;


class SubReplyObserver
{
    private $input;

    public function __construct(Request $request)
    {
        $this->input = $request->all();
    }

    public function created(SubReply $ticketSubReply)
    {
      if(isset($this->input['content'])) {
        $ParentReply = Reply::find($ticketSubReply->reply_id);
        $ParentReply->subRepliesNum += 1;
        $ParentReply->save();
        $ticket = Ticket::find($ParentReply->ticket_id);

        $filesPaths = [];
        if(isset($this->input['files'])){

            $files = $this->input['files'];

            foreach ($files as $file) {

                $fileName = $file->getClientOriginalName();
                $fullFileName = Carbon::now().'-'.$fileName;
                $TicketFiles = new TicketDialogFiles();
                $TicketFiles->create([
                    'file_path' => 'public/tickets/'.$ticket->id.'/ticketDialog/'.$fullFileName,
                    'ticket_id' => $ticket->id,
                ]);
                $file->storeAs('public/tickets/'.$ticket->id. '/ticketDialog/',$fullFileName);
                $path = 'app/public/tickets/'.$ticket->id.'/ticketDialog/'.$fullFileName;

                array_push($filesPaths, ['path' => $path,'name' => $fileName, 'type' => $file->getClientmimeType()]);

            }

        }


        $oldReplies = [];
        if($ticket->returnOldReplies($ticketSubReply->id) && count($ticket->returnOldReplies($ticketSubReply->id)) >= 1){

            $oldRepliesObjects = SubReplyResource::collection($ticket->returnOldReplies($ticketSubReply->id));

            foreach ($oldRepliesObjects as $replyContent){
                array_push($oldReplies,$replyContent);
            }
        }

        $MailsArr = [];
        $owners = $ticket->owner()->get();

        foreach ($owners as $owner){
          if($owner->support === 0) {
            array_push($MailsArr, $owner->email);
          }
        }


          $Cc_arr = [];
          if(isset($this->input['cc'])){
              $requestCc =  explode(",",request()->cc);

              foreach ( $requestCc as $cc){
                  array_push($Cc_arr, $cc);
              }
          }

          $Bcc_ar = [];
          array_push($Bcc_ar,config('mail.bcc'));
          if(isset($this->input['bcc'])){
              $Bccs =  explode(",",request()->bcc);
              foreach ( $Bccs as $Bcc){
                  if($Bcc == config('mail.bcc')){
                      continue;
                  }
                  array_push($Bcc_ar, $Bcc);
              }
          }
        TicketReplyJob::dispatch($ticketSubReply,$oldReplies, $filesPaths, $MailsArr, $ticket, $Cc_arr,$Bcc_ar,auth()->user()->email);

      }
    }

}
