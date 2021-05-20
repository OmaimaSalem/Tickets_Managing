<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use Validator;
use Modules\Versions\Entities\Version;

class HomeController extends Controller
{
    public function index()
    {
        $versions = Version::orderby('id','desc')->get(['id','version']);
        return view('frontend.home.welcome',compact('versions'));
    }

    public function contactPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) { 
            return redirect()->to('/'.'#contact-section')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        Mail::send(
            'emails.contact-form-mail',
            [
                'name' => $request->fname . ' ' . $request->lname,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'comment' => $request->message
            ],
            function ($message) use ($request) {
                $message->from('kontakt@tecsee.de');
                $message->replyTo($request->email, $request->fname);
                $message->to('support@tecsee.de')
                    ->subject('Message from Alfacockpit Kontakt');
            }
        );

        return redirect()->to('/'.'#contact-section')->with('success', 'Vielen Dank für Ihre Kontaktaufnahme. Wir werden uns bald bei Ihnen melden!');
    }

    public function impressum()
    {
        return view('frontend.home.impressum');
    }

    public function agb()
    {
        return view('frontend.home.agb');
    }
}
