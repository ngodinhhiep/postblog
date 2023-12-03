<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustBeLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->check()) { // if the user is logged in, accept the request to the a specific handler
            return $next($request);
        }
        return redirect('/')->with('failure', 'you must be logged in'); // if the user is not logged in, redirect to the homepage
    }
}
