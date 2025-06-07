<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class ApplicantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {   
        if(Auth::check() && Auth::user()->role === 'applicant'){
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

}
