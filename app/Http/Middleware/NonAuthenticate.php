<?php

namespace App\Http\Middleware;
use Closure,Illuminate\Support\Facades\Auth;

class NonAuthenticate
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
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return $next($request);
    }
}
