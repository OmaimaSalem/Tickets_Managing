<?php

namespace Modules\MailTemplate\Http\Controllers;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\API\sendResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\MailTemplate\Entities\MailTemplate;
use Modules\MailTemplate\Http\Requests\AddTemplateRequest;
use Modules\MailTemplate\Jobs\MailTemplateJob;
use Modules\MailTemplate\Http\Requests\DeleteTemplateRequest;
use Modules\MailTemplate\Http\Requests\UpdateTemplateRequest;
use Modules\MailTemplate\Http\Resources\TemplateCollection;
use Modules\MailTemplate\Http\Resources\TemplateResource;
use Modules\MailTemplate\Entities\MailTemplateAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class MailTemplateController extends BaseController
{
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
      $this->middleware('permission:mail-template|ticket-management');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $templates = MailTemplate::all();
      return $this->sendResponse(new TemplateCollection($templates), 'Templates retrieved successfully.');
    }

    public function send(Request $request)
    {
        // dd($request->all());
      // if ($request->hasFile('files')) {
      //   $files = $request->file('files');
      //   if(count($files) > 1) {
      //     $filenameArray = array();
      //     foreach($files as $file) {
      //       $extension = $file->getClientOriginalExtension();
      //       $name = $file->getClientOriginalName();
      //       $filename  = $name . time() . '.' . $extension;
      //       $path = 'public/templates_files/';
      //       $file->storeAs($path, $filename);
      //       $fileLink = Storage::url('templates_files/'. $filename);
      //       array_push($filenameArray, $filename);
      //       $request->merge(['attachments' => $filenameArray]);
      //     }
      //   }elseif(count($files) == 1) {
      //     $file = $request->file('files')[0];
      //     $extension = $file->getClientOriginalExtension();
      //     $name = $file->getClientOriginalName();
      //     $filename  = $name . time() . '.' . $extension;
      //     $path = 'public/templates_files/';
      //     $file->storeAs($path, $filename);
      //     $fileLink = Storage::url('templates_files/'. $filename);
      //     $request->merge(['attachments' => $filename]);
      //   }
      //   MailTemplateJob::dispatch($request);
      //   return $this->sendResponse(null, 'Mail sent successfully.');
      // }
      if(strpos($request->touser, ",")) {
        $mymails = explode(',', $request->touser);
        $mails = array();
        foreach($mymails as $mail) {
          array_push($mails, $mail);
        }
        $emails = $mails;
      } else {
        $emails = $request->touser;
      }
      Mail::send('emails.reply', ['body' => $request->body], function($message) use ($emails, $request) {
             $message->to($emails)->subject($request->subject);
           if ($request->hasFile('files')) {
             if(count($request->file('files')) > 1) {
               $files = $request->file('files');
               foreach ($files as $file) {
                 $message->attach($file, ['as' => $file->getClientOriginalName(), 'mime' => $file->getClientmimeType()]);
               }
             } elseif(count($request->files) == 1) {
               $file = $request->file('files')[0];
               $message->attach($file, ['as' => $file->getClientOriginalName(), 'mime' => $file->getClientmimeType()]);
             }
           }
       });

      // MailTemplateJob::dispatch($request);
      return $this->sendResponse(null, 'Mail sent successfully.');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(AddTemplateRequest $request)
    {
      $input = $request->validated();
      $input['created_at'] = Carbon::now();
      $input['created_by'] = auth()->user()->id;
      try {
        if ($request->hasFile('file')) {
          $allowedfileExtension = ['jpg', 'png', 'pdf', 'xls', 'xlsx'];
          $file = $request->file('file');
          $extension = $file->getClientOriginalExtension();
          $name = $file->getClientOriginalName();
          $check = in_array($extension, $allowedfileExtension);
          if ($check) {
              $filename  = $name . time() . '.' . $extension;
              $path = 'public/templates_files/';
              $file->storeAs($path, $filename);
              $fileLink = Storage::url('templates_files/'. $filename);

              $request->merge(['file' => $filename]);
          } else {
              return response()->json(['invalid_file_format'], 422);
          }
          $template = MailTemplate::create($input);
          $attachment = new MailTemplateAttachment();
          $attachment->mail_template_id = $template->id;
          $attachment->attachment_path = $fileLink;
          $attachment->created_by = auth()->user()->id;
          $attachment->save();
        } else {
          $template = MailTemplate::create($input);
        }

      } catch (Exception $ex) {
        throw new ItemNotCreatedException('Category');
      }

      return $this->sendResponse(new TemplateResource($template), 'Template created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('mailtemplate::show');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateTemplateRequest $request, $id)
    {
      $input = $request->validated();
      $template = MailTemplate::find($id);
      if(!$template) {
        throw new ItemNotFoundException('Template');
      }
      $template->updated_at = Carbon::now();
      $template->updated_by = auth()->user()->id;

      try {
          $updated = $template->fill($input)->save();
      } catch (\Exception $ex) {
        dd($ex->getMessage());
          throw new ItemNotUpdatedException('Template');
      }

      if (!$updated)
      throw new ItemNotUpdatedException('Template');

      return $this->sendResponse(new TemplateResource($template), 'Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $template = MailTemplate::find($id);
      if(!$template) {
          throw new InvalidDataException('Can\'t delete!, Category has templates.');
      }

      try {
          $template->delete();
      } catch (\Exception $ex) {
          throw new ItemNotDeletedException('Category');
      }

        return $this->sendResponse(new TemplateResource($template), 'Category deleted successfully.');
    }
}
