<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['guest', 'checkIfNotClient'])->except('logout');
    }

    public function validateLogin(Request $request) {
        // dd($request->email);

        $rules = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        // $subdomain = array_first(explode('.', request()->getHost()));
        // if($subdomain != 'dev') {
        //     $rules['g-recaptcha-response'] = 'required|captcha';
        // }
        
        $request->validate($rules);
    }
}
