<?php

namespace App\Http\Middleware\Session;

use Closure;
use App\Helpers\Session;
use App\User;

class CheckSessionSilent
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
        // Check if session token exists
        $token = $request->header('Session-Token');
        if (!$token) {
            return $next($request);
        }

        // Check if session token is valid
        $sessionData = Session::verify($token);
        if (!$sessionData) {
            return $next($request);
        }

        // Check if session has expired
        if (time() > strtotime($sessionData->expires)) {
            return $next($request);
        }

        // Check if session user exists
        $user = User::find($sessionData->user_id);
        if (!$user) {
            return $next($request);
        }

        $request->attributes->add([
            'session_data' => $sessionData,
            'user' => $user
        ]);

        return $next($request);
    }
}
