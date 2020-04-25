<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsVerified
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
        if (Auth::check() && Auth::user()->email_verified_at != null) {
            return $next($request);
        }
        else{
            $response = [
                'error' => 'Your Email is Not Verified',
            ];
            return response()->json($response, 400);
        }
    }
}
