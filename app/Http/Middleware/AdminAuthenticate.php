<?php

namespace App\Http\Middleware;
use Closure,Illuminate\Support\Facades\Auth;

class AdminAuthenticate
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
        if(empty(Auth::user()) || Auth::user()->role!=config('roles.system.ADMIN')){
            return redirect('/login');
        }
        return $next($request);
    }
}
