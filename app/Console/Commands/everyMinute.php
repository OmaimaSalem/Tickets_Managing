<?php
namespace App\Console\Commands;

ini_set('memory_limit', '1024M');

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Jobs\User\NewAccountJob;
use App\Models\DiscussionFiles;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionReply;
use App\Models\Ticket;
use App\Models\TicketCollection;
use App\Models\Ticket_file;
use App\Models\Ticket_mail;
use App\Models\User;
use Carbon\Carbon;
use EmailReplyParser\Parser\EmailParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Modules\OfferConversations\Entities\OfferConversation;
use Modules\Offer\Entities\Offer;
use Modules\SpamTickets\Entities\SpamTickets;
use Modules\TicketConversation\Entities\TicketConversation;
use Modules\TicketDialog\Entities\Reply;
use Modules\TicketDialog\Entities\SubReply;
use Modules\TicketDialog\Entities\TicketDialogFiles;
use Modules\TicketDialog\Entities\TicketSubReplyCC;
use Validator;
use Webklex\IMAP\Facades\Client;
use App\Notifications\Ticket\ClientReplyNotification;
use Mail;


class everyMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:refreshMailBoxToCreateTickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To fetch new mails to create new tickets';
    private $ticketCollection;
    protected $ticketNumber, $ticketReplyNumber;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $oClient = Client::account('default');
        $oClient->connect();
        $aFolder = $oClient->getFolder('INBOX');

        $aMessage = $aFolder->query()->unseen()->limit(10)->OLD()->setFetchAttachment(true)->leaveUnread()->get()->reverse();
        foreach($aMessage as $oMessage){
            $emailData = [];
            $emailData['email_id']  = $oMessage->getMessageId();
            $emailData['reference'] = $oMessage->getAttributes()['references'];

            // \Log::info('---------------------------------------------------------');
            // \Log::info(print_r(imap_mime_header_decode(imap_utf8($oMessage->getSubject()))));
            // \Log::info(print_r(imap_mime_header_decode($oMessage->getSubject())));
            // \Log::info($oMessage->getSubject());
            // \Log::info('---------------------------------------------------------');
            $emailData['subject']   = isset(imap_mime_header_decode(imap_utf8(str_replace('/','',$oMessage->getSubject())))[0]) ? imap_mime_header_decode(imap_utf8(str_replace('/','',$oMessage->getSubject())))[0]->text : ($oMessage->getSubject() ? $oMessage->getSubject() : 'No Subject') ;
            $emailData['subject']   = mb_convert_encoding($emailData['subject'], 'UTF-8', 'UTF-8');

            try {
                //imap_mime_header_decode(imap_utf8($oMessage->getSubject()));
                    if(isset($oMessage->getReplyTo()[0])) {
                        $emailData['email'] = $oMessage->getReplyTo()[0]->mail;
                        $emailData['personal'] = $oMessage->getReplyTo()[0]->personal;
                    } else {
                        $oMessage->setFlag(['Seen']);
                        $oMessage->moveToFolder('FAILS');
                    }

                    if ($oMessage->hasHTMLBody()) {
                        $emailData['body'] =  (imap_utf8($oMessage->getHTMLBody(true)));
                    } else {
                        $emailData['body'] =  (imap_utf8($oMessage->getTextBody(true)));
                        $emailData['body'] = nl2br($emailData['body'], false);
                    }

                    $emailData['body'] = preg_replace("/<base.*?>/im","$1",$emailData['body']);

                    // dd($oMessage->getReferences());
                    // attachments
                    $emailData['attachmentPaths'] = [];
                    foreach ($oMessage->getAttachments() as $oAttachment) {
                        // validate extention
                        if (in_array($oAttachment->getExtension(),
                                    [
                                        'png',
                                        'jpg',
                                        'jpeg',
                                        'txt',
                                        'csv',
                                        'docx',
                                        'doc',
                                        'xls',
                                        'pdf'
                                    ]
                                )
                            ){
                        // storage

                            if(!$this->checkDiscussionSubject($emailData['subject']) || $this->checkTicketSubject($emailData['subject']) == 'subReply' || $this->checkTicketSubject($emailData['subject']) == 'reply'){

                                $attachmentPath = storage_path('app/public/attachments/' . $oMessage->getMessageId() . '/' . $oAttachment->name);
                                $dirName = dirname($attachmentPath);
                                if (!is_dir($dirName))
                                    mkdir($dirName, 0755, true);

                                $fp = fopen($attachmentPath, "wb");
                                file_put_contents($attachmentPath, $oAttachment->content);
                                fclose($fp);

                                $emailData['attachmentPaths'][] = $attachmentPath;
                                
                                $http_path = explode('public/',$attachmentPath);
                                $http_path = url('/storage/'.$http_path[1]);
                                $emailData['body'] =  str_replace('cid:'.$oAttachment->id,$http_path, $emailData['body']);

                            }

                        }
                    }




                    // $emailData['project'] = $this->getProjectByClientEmail($emailData, $oMessage);
                    $client = $this->getClient($emailData, $oMessage);
                    if ( $this->checkDiscussionSubject($emailData['subject'])) { //condition for project discussions replies
                        $this->createDiscussion($emailData, $oMessage);
                    }
                    elseif ($this->checkTicketSubject($emailData['subject']) || $this->checkTicketSubject($emailData['subject']) == 'reply'){
                        $this->replyTicket($emailData, $oMessage);
                    } elseif ($this->checkTicketSubject($emailData['subject']) || $this->checkTicketSubject($emailData['subject']) == 'subReply'){
                        $this->subReplyTicket($emailData, $oMessage);
                    } elseif($this->checkOfferSubject($emailData['subject'])) {
                        $this->updateOffer($emailData, $oMessage);
                    }elseif($client->spam == 1) {
                        $this->createSpamTicket($emailData, $oMessage);
                    } else {
                        $this->createNewTicket($emailData, $oMessage);
                    }

                    //Move the current Message to 'INBOX.read'
                    $oMessage->setFlag(['Seen']);




            } catch (\Throwable $th) {
                //throw $th;
                \Log::info($th);
                $oMessage->setFlag(['Unseen']);
                Mail::raw("the  ticket ## ".$emailData['subject']." not inserted to the system", function ($message) {
                    $message->subject('System error');
                    $message->to([env('EMAIL_EXCEPTION_TO'),env('EMAIL_EXCEPTION_CC'),env('EMAIL_EXCEPTION_BCC')]);
                });
            }


            



        }
    }

    // private function getProjectByClientEmail($emailData, $oMessage)
    // {
    //     $client = $this->getClient($emailData, $oMessage);

    //     /**
    //      * if client has one project with open status add new tickets to it
    //      */
    //     $count = Project::where('owner_id', $client->id)->count();
    //     if ($count == 1) {
    //         return Project::where('owner_id', $client->id)
    //                       ->first();
    //     }

    //     /**
    //      * else return other project
    //      */
    //     $project = Project::where('name', 'other')
    //                       ->where('owner_id', $client->id)
    //                       ->first();

    //     /**
    //      * else create new project with name other
    //      */
    //     if (! $project) {
    //         $project = $this->createOtherProject($client, $oMessage);
    //     }

    //     return $project;
    // }

    // private function createOtherProject($client, $oMessage)
    // {
    //     $project = new Project();
    //     $project->name = $oMessage->getSubject();
    //     $project->owner_id = $client->id;
    //     $project->description = 'This for a collection of tickets to be filtered';
    //     $project->created_by = 1;
    //     $project->created_at = Carbon::now();
    //     $project->task_rate = 0;
    //     $project->budget_hours = 0;

    //     try {
    //         $project->save();
    //     } catch (Exception $ex) {
    //         $oMessage->setFlag(['Seen']);
    //         throw new ItemNotCreatedException('Project', $ex->getMessage(), 'E-Mail-Inbox', $oMessage->getSubject());
    //     }

    //     return $project;
    // }

    private function getClient($emailData, $oMessage)
    {
        $client = User::where('email', $emailData['email'])
                      ->where('type', 'client')
                      ->first();

        if (!$client) {
            $this->validateClientData($emailData, $oMessage);
            $client = $this->createNewClient($emailData, $oMessage);
        }

        return $client;
    }

    private function createNewClient($emailData, $oMessage)
    {
        $user = new User();
        if($emailData['personal'] == '') {
            $email_array = explode("@",$emailData['email']);
            // $emailData['personal'] = mb_convert_encoding($email_array[0], 'HTML-ENTITIES', 'UTF-8');
            $user->name = mb_convert_encoding($email_array[0], 'HTML-ENTITIES', 'UTF-8');

        } else {
            $user->name = mb_convert_encoding($emailData['personal'], 'HTML-ENTITIES', 'UTF-8');
        }
        $user->email = mb_convert_encoding($emailData['email'], 'HTML-ENTITIES', 'UTF-8');

        $password = Hash::make(str_random(8));
        $user->password = $password;
        $user->type = 'client';
        $user->vacation = 0;
        $user->created_by = 1;
        $user->created_at = Carbon::now();

        try {
            $user->save();
        } catch (Exception $ex) {
            $oMessage->setFlag(['Seen']);
            throw new ItemNotCreatedException('User', $ex->getMessage(), 'E-Mail-Inbox', $emailData['subject']);
        }

        // no-need to send mail now, user will click forget password
        //NewAccountJob::dispatch($user, $password);

        return $user;
    }

    private function validateClientData($emailData, $oMessage)
    {
        $validator = Validator::make($emailData, [
            'email' => 'string|email|unique:users'
        ]);

        if ($validator->fails()) {
            $oMessage->setFlag(['Seen']);
            throw new ItemNotCreatedException('User', $validator->errors(), 'E-Mail-Inbox', $emailData['subject']);
        }

    }

    private function createNewTicket($emailData, $oMessage)
    {
        $this->ticketCollection = TicketCollection::pluck('collection','email')->toArray();

        $ticket = new Ticket();
        $client = $this->getClient($emailData, $oMessage);
        $ticket->email_id = $emailData['email_id'];
        // $ticket->project_id = $emailData['project']->id;
        $ticket->name = $emailData['subject'];
        if($oMessage->hasHTMLBody()) {
          $emailData['body'] = str_replace('Dev', '', $emailData['body']);
          $data = new \DOMDocument;
          @$data->loadHTML(mb_convert_encoding($emailData['body'], 'HTML-ENTITIES', 'UTF-8'));
          $emailData['body'] = $data->saveHTML($data);
        }
        $ticket->created_by = $client->id;
        $ticket->created_at = Carbon::parse($oMessage->getDate());
        $ticket->updated_by = $client->id;
        $ticket->updated_at = Carbon::parse($oMessage->getDate());
        $ticket->read = 0;
        $ticket->estimated_time = 0;
        $ticket->description = $emailData['body'];
        // $ticket->owner_id = $client->id;
        $ticket->collection = isset($this->ticketCollection[$emailData['email']]) ? $this->ticketCollection[$emailData['email']] : null;

        try {
            $ticket->save();
            $ticket->manager()->attach(1);
            $ticket->owner()->attach($client->id);
        } catch (Exception $ex) {
            $oMessage->setFlag(['Seen']);
            throw new ItemNotCreatedException('Ticket', $ex->getMessage(), 'E-Mail-Inbox', $emailData['subject']);
        }
        $oMessage->setFlag(['Seen']);
        $oMessage->moveToFolder('TICKETS');

        // insert attachment
        $this->insertAttachmentFiles($ticket, $emailData, $oMessage);

        // insert cc
        $this->insertCCMails($ticket, $oMessage);

        echo nl2br('email: '.$emailData['subject'].' is inserted as a ticket id = '.$ticket->id);
        echo "<br>";
    }

        private function createSpamTicket($emailData, $oMessage)
    {
        $ticket = new SpamTickets();
        $client = $this->getClient($emailData, $oMessage);
        $ticket->name = $emailData['subject'];
        if($oMessage->hasHTMLBody()) {
          $emailData['body'] = str_replace('Dev', '', $emailData['body']);
          $data = new \DOMDocument;
          @$data->loadHTML(mb_convert_encoding($emailData['body'], 'HTML-ENTITIES', 'UTF-8'));
          $emailData['body'] = $data->saveHTML($data);
        }
        $ticket->created_by = $client->id;
        $ticket->created_at = Carbon::parse($oMessage->getDate());
        $ticket->updated_at = Carbon::parse($oMessage->getDate());
        $ticket->read = 0;
        $ticket->description = $emailData['body'];
        $ticket->owner_id = $client->id;
        try {
            $ticket->save();

        } catch (Exception $ex) {
            $oMessage->setFlag(['Seen']);
            throw new ItemNotCreatedException('Ticket', $ex->getMessage(), 'E-Mail-Inbox', $emailData['subject']);
        }
        $oMessage->setFlag(['Seen']);
        $oMessage->moveToFolder('SPAM_TICKETS');

        // insert attachment
        $this->insertAttachmentFiles($ticket, $emailData, $oMessage);

        echo nl2br('email: '.$emailData['subject'].' is inserted as a spam ticket id = '.$ticket->id);
        echo "<br>";
    }

    private function insertAttachmentFiles($ticket, $emailData, $oMessage)
    {
        if (isset($emailData['attachmentPaths'])) {
            foreach ($emailData['attachmentPaths'] as $attachmentPath) {
                $file = new Ticket_file();
                $file->attachment_path = $attachmentPath;
                $file->ticket_id = $ticket->id;
                $file->created_by = 1;

                try {
                    $file->save();
                } catch (Exception $ex) {
                    $oMessage->setFlag(['Seen']);
                    throw new ItemNotCreatedException('Ticket_file', $ex->getMessage(), 'E-Mail-Inbox', $emailData['subject']);
                }
            }
        }

    }

    private function insertCCMails($ticket, $oMessage)
    {
        if ($oMessage->getCc()) {
            foreach ($oMessage->getCc() as $ccMail) {
                $ticket_cc_mail = new Ticket_mail();
                $ticket_cc_mail->email = $ccMail->mail;
                $ticket_cc_mail->ticket_id = $ticket->id;
                $ticket_cc_mail->created_by = 1;
                try {
                    $ticket_cc_mail->save();
                } catch (Exception $ex) {
                    $oMessage->setFlag(['Seen']);
                    throw new ItemNotCreatedException('Ticket_mail', $ex->getMessage(), 'E-Mail-Inbox', $oMessage->getSubject());
                }
            }
        }
    }

    private function insertSubRepliesCCMails($id, $oMessage)
    {
        if ($oMessage->getCc()) {
            foreach ($oMessage->getCc() as $ccMail) {
                $subReplyCCMail = new TicketSubReplyCC();
                $subReplyCCMail->email = $ccMail->mail;
                $subReplyCCMail->sub_reply = $id;
                $subReplyCCMail->created_by = 1;
                try {
                    $subReplyCCMail->save();
                } catch (Exception $ex) {
                    $oMessage->setFlag(['Seen']);
                    throw new ItemNotCreatedException('Ticket_mail', $ex->getMessage(), 'E-Mail-Inbox', $oMessage->getSubject());
                }
            }
        }
    }

    private function replyTicket($emailData, $oMessage)
    {
        $client = $this->getClient($emailData, $oMessage);
        $subject = explode('##', $emailData['subject']);
        if($subject[1]) {
            $ticket = Ticket::where('number', 'like', '%' . $subject[1] . '%')
                    ->first();
        } else {
            $ticket = null;
        }
        if (! $ticket) {
            $this->createNewTicket($emailData, $oMessage);
        } else {
           $reply_content = "";

          if ($oMessage->hasHTMLBody()) {
            if(stristr($emailData['body'], '<!--explodehere-->')) {
                $title = '<!--explodehere-->';
                $descriptionx = explode($title, $emailData['body']);
                $data = new \DOMDocument;
                @$data->loadHTML(mb_convert_encoding($descriptionx[0], 'HTML-ENTITIES', 'UTF-8'));
                $descriptionx[0] = $data->saveHTML($data);
            }
            else {
              $title = '##- Bitte geben Sie Ihre Antwort über dieser Zeile ein. -##';
              $descriptionx = explode($title, $emailData['body']);
              $data = new \DOMDocument;
              @$data->loadHTML(mb_convert_encoding($descriptionx[0], 'HTML-ENTITIES', 'UTF-8'));
              $descriptionx[0] = $data->saveHTML($data);
            }
          } else {
              $emailData['body'] =  $oMessage->getTextBody(true);
              $emailData['body'] = nl2br($emailData['body'], false);
              $title = '##- Bitte geben Sie Ihre Antwort über dieser Zeile ein. -##';
              $descriptionx = explode($title, $emailData['body']);
            }
            $ticket->reply = 0;

            if($ticket->status_id == 2 || $ticket->status_id == 4) {
                $ticket->status_id = 1;
            }
            try {
                $ticket->updated_by = $client->id;
                $ticket->updated_at = Carbon::parse($oMessage->getDate());
                $ticket->save();
                if($this->checkTicketSubject($emailData['subject']) == 'reply') {
                    $reply = new Reply();
                    $Ticket_id = $ticket->id;
                    $reply->ticket_id = $ticket->id;
                    $reply->subject = $emailData['subject'];
                    $reply->content = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $descriptionx[0]);
                    $reply->created_by = $client->id;
                    $reply->created_at = Carbon::parse($oMessage->getDate());
                    $reply->save();
                    $reply_content = $reply->content;
                    $this->insertCCMails($ticket, $oMessage);
                } elseif($this->checkTicketSubject($emailData['subject']) == 'subReply') {
                    $reply = Reply::find($this->ticketReplyNumber);
                    $subReply = new subReply();
                    $Ticket_id = $reply->ticket_id;
                    $subReply->reply_id = $reply->id;
                    $subReply->subject = $emailData['subject'];
                    $subReply->content = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $descriptionx[0]);
                    $subReply->created_by = $client->id;
                    $subReply->created_at = Carbon::parse($oMessage->getDate());
                    $subReply->save();
                    $reply_content = $subReply->content;

                    $this->insertSubRepliesCCMails($reply->id, $oMessage);
                }
            } catch (Exception $th) {
                $oMessage->setFlag(['Seen']);
                throw new ItemNotUpdatedException('Ticket', $ex->getMessage(), 'E-Mail-Inbox', $emailData['subject']);
            }

            //storing email attachments
            foreach ($oMessage->getAttachments() as $oAttachment) {
                if (in_array($oAttachment->getExtension(),
                    [
                        'png',
                        'jpg',
                        'jpeg',
                        'txt',
                        'csv',
                        'docx',
                        'doc',
                        'xls',
                        'pdf'
                    ]
                )
                ){

                    $fileName = $oAttachment->name;

                    $fullFileName = Carbon::now().'-'.$fileName;

                    $TicketFiles = new TicketDialogFiles();

                    $TicketFiles->create([
                        'file_path' => 'public/tickets/'.$Ticket_id.'/ticketDialog/'.$fullFileName,
                        'ticket_id' => $Ticket_id,
                    ]);

                    $attachmentPath = storage_path('app/public/tickets/'.$Ticket_id .'/ticketDialog/'.$fullFileName);
                    $dirName = dirname($attachmentPath);
                    if (!is_dir($dirName))
                        mkdir($dirName, 0755, true);

                    $fp = fopen($attachmentPath, "wb");
                    file_put_contents($attachmentPath, $oAttachment->content);
                    fclose($fp);


                }
            }

            $oMessage->setFlag(['Seen']);
            $oMessage->moveToFolder('TICKETS');

            // insert attachment
            $this->insertAttachmentFiles($ticket, $emailData, $oMessage);

            // \Log::info($ticket->manager);
            $ticket->manager->each(function($manager) use($ticket,$reply_content) {
                $manager->notify(new ClientReplyNotification($ticket,$reply_content));
            });

            echo nl2br('email: '.$emailData['subject'].' is updated in the ticket id = '.$ticket->id);
            echo "<br>";
        }
    }

    private function checkTicketSubject($subject) {
        $check_subject = explode('##', $subject);
        // dd($check_subject);
        if(isset($check_subject[1])) {
        $ticket = Ticket::where('number', 'like', '%' . $check_subject[1] . '%')
                        ->first();
        } else {
            $ticket = null;
        }
        if ($ticket) {
            $this->ticketNumber = $check_subject[1];
            
            if(isset($check_subject[2])){
                $check_reply = explode('-', $check_subject[2]);
                if(isset($check_reply[0])) {
                    $subReply = Reply::find($check_reply[0]);
                    if($subReply) {
                        $this->ticketReplyNumber = $check_reply[0];
                        return 'subReply';
                    } else {
                        return 'reply';
                    }
    
                }
            }else {
                return 'reply';
            }


        } else {
            return false;
        }
    }

    private function checkOfferSubject($subject) {
      $check_offer = explode('##', $subject);
      if(isset($check_offer[1])) {
        $offer_name = explode('-', $check_offer[1]);
        if($offer_name[0] == 'Offer') {
          return true;
        }
        return false;
      }
    }

    private function updateOffer($emailData, $oMessage)
    {
        $check_offer = explode('##', $emailData['subject']);
        if(isset($check_offer[1])) {
          $offer_name = explode('-', $check_offer[1]);
          if($offer_name[0] == 'Offer') {
            $offer = Offer::where('number', 'like', '%' . $offer_name[1] . '%')
                    ->first();
          }
        }  else {
          $offer = null;
        }
        if ($offer) {
          if ($oMessage->hasHTMLBody()) {
            if(stristr($emailData['body'], '<!--explodehere-->')) {
                $title = '<!--explodehere-->';
                $descriptionx = explode($title, $emailData['body']);
                $data = new \DOMDocument;
                @$data->loadHTML(mb_convert_encoding($descriptionx[1], 'HTML-ENTITIES', 'UTF-8'));
                $descriptionx[1] = $data->saveHTML($data);
                $mail_text = $descriptionx[1];
            }
            else {
              $title = '##- Bitte geben Sie Ihre Antwort über dieser Zeile ein. -##';
              $descriptionx = explode($title, $emailData['body']);
              $data = new \DOMDocument;
              @$data->loadHTML(mb_convert_encoding($descriptionx[0], 'HTML-ENTITIES', 'UTF-8'));
              $descriptionx[0] = $data->saveHTML($data);
              $mail_text = $descriptionx[0];
            }
          } else {
                $emailData['body'] =  $oMessage->getTextBody(true);
                $emailData['body'] = nl2br($emailData['body'], false);
                $title = '##- Bitte geben Sie Ihre Antwort über dieser Zeile ein. -##';
                $descriptionx = explode($title, $emailData['body']);
                $mail_text = $descriptionx[0];
              }
            try {
              $offerConversation = new OfferConversation();
              $offerConversation->offer_id = $offer->id;
              $offerConversation->created_by = 1;
              $offerConversation->created_at = Carbon::now();
              $offerConversation->conversation = '<div class="card card-primary card-outline">
                                                    <div class="card-body p-0">
                                                        <div class="mailbox-read-info">
                                                            <h5>' . $check_offer[2] . '</h5>
                                                            <h6>From: ' . $oMessage->getReplyTo()[0]->mail . '
                                                                <span class="mailbox-read-time float-right">' . Carbon::now()->isoFormat('LLLL') . '</span>
                                                            </h6>
                                                        </div>
                                                        <div class="mailbox-read-message">
                                                            <p>'. $mail_text .'</p>
                                                        </div>
                                                    </div>
                                                </div>';
              $offerConversation->save();
            } catch (Exception $th) {
                $oMessage->setFlag(['Seen']);
                throw new ItemNotUpdatedException('OfferConversation', $ex->getMessage(), 'E-Mail-Inbox', $emailData['subject']);
            }
            $oMessage->setFlag(['Seen']);
            $oMessage->moveToFolder('OFFERS');

            // insert attachment
            $this->insertAttachmentFiles($offerConversation, $emailData, $oMessage);

            // insert cc
            $this->insertCCMails($offerConversation, $oMessage);

            echo nl2br('email: '.$emailData['subject'].' is updated in the offer id = '.$offer->id);
            echo "<br>";
        }
    }

    //project discussions functions

    public function checkDiscussionSubject($subject){ //check if the mail is a reply for existing discussion
            $check_subject = explode('##', $subject);
        if(isset($check_subject[1])) {
            $discussion = ProjectDiscussion::where('id', 'like', '%' . $check_subject[1] . '%')
                ->first();
        } else {
            $discussion = null;
        }
        if ($discussion) {
            return true;
        } else {
            return false;
        }
    }

    public function createDiscussion($emailData, $oMessage){ //creating a new discussion reply

        $DiscussionId = explode('##', $emailData['subject']);

        $DiscussionTitle = $emailData['subject'];

        $DiscussionClient = $this->getClient($emailData, $oMessage); //get created by id

        $discussion = ProjectDiscussion::find($DiscussionId[1]);

        //get email body
        if ($oMessage->hasHTMLBody()) {
            if(stristr($emailData['body'], '<!--explodehere-->')) {
                $title = '<!--explodehere-->';
                $DiscussionContent = explode($title, $emailData['body']);
                $data = new \DOMDocument;
                @$data->loadHTML(mb_convert_encoding($DiscussionContent[0], 'HTML-ENTITIES', 'UTF-8'));
                $DiscussionContent[0] = $data->saveHTML($data);
            }
            else {
                $title = '##- Bitte geben Sie Ihre Antwort über dieser Zeile ein. -##';
                $DiscussionContent = explode($title, $emailData['body']);
                $data = new \DOMDocument;
                @$data->loadHTML(mb_convert_encoding($DiscussionContent[0], 'HTML-ENTITIES', 'UTF-8'));
                $DiscussionContent[0] = $data->saveHTML($data);
            }
        } else {
            $emailData['body'] =  $oMessage->getTextBody(true);
            $emailData['body'] = nl2br($emailData['body'], false);
            $title = '##- Bitte geben Sie Ihre Antwort über dieser Zeile ein. -##';
            $DiscussionContent = explode($title, $emailData['body']);
        }

        $replyData = [
            'title' => $DiscussionTitle,
            'discussion_id' => $DiscussionId[1],
            'content' => $DiscussionContent[0],
            'created_at' => Carbon::now(),
            'created_by' => $DiscussionClient->id,
            'updated_at' => Carbon::now()
        ];

        //creating a new reply for existing discussion
        $reply = ProjectDiscussionReply::create($replyData);

        //saving the mail as a reply
        $reply->reply = 1;
        $reply->save();

        //inc discussion comments number
        $discussion->addReply($DiscussionId[1]);

        //adding unseen reply flag to the discussion
        $discussion->seen = 0;
        $discussion->save();


        //storing email attachments
        foreach ($oMessage->getAttachments() as $oAttachment) {
            if (in_array($oAttachment->getExtension(),
                [
                    'png',
                    'jpg',
                    'jpeg',
                    'txt',
                    'csv',
                    'docx',
                    'doc',
                    'xls',
                    'pdf'
                ]
            )
            ){

                $fileName = $oAttachment->name;


                $dirName = $DiscussionId[1];

                $fullFileName = Carbon::now().'-'.$fileName;

                $finalPath = $dirName.'/'.$fullFileName;

                $DiscussionFile = new DiscussionFiles();

                $DiscussionFile->create([
                    'file_path' => 'public/projects/'.$discussion->project_id.'/discussion/'.$finalPath,
                    'discussion_id' => $DiscussionId[1],
                ]);

                $attachmentPath = storage_path('app/public/projects/' . $discussion->project_id . '/discussion/' . $DiscussionId[1] . '/' . Carbon::now() . '-' . $fileName);
                $dirName = dirname($attachmentPath);
                if (!is_dir($dirName))
                    mkdir($dirName, 0755, true);

                $fp = fopen($attachmentPath, "wb");
                file_put_contents($attachmentPath, $oAttachment->content);
                fclose($fp);


            }
        }

        $oMessage->setFlag(['Seen']);
        $oMessage->moveToFolder('DISCUSSIONS');

        echo nl2br('discussion: '.$emailData['subject'].' is created as a reply for discussion  = '.$DiscussionId[1]);
        echo "<br>";

    }
}
