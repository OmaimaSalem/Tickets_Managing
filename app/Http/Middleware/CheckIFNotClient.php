<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;

class CheckIFNotClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request != [] && $request->has('email')){
            $user = User::where('email', $request->email)->first();
            if($user && $user->type == 'client'){
                return back()->with('error', 'These credentials do not match our records.');
            }else{
                return $next($request); 
            }
        }
        return $next($request);
    }
}
