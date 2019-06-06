<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Response;
use App\Helpers\Session;
use App\User;

class CheckSessionToken
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
        $token = $request->header('Session-Token');
        if (!$token) {
            return Response::unauthorized(['message' => 'Token is required']);
        }

        $sessionData = Session::verify($token);
        if (!$sessionData) {
            return Response::unauthorized(['message' => 'Token is not valid']);
        } elseif (time() > strtotime($sessionData->expires)) {
            return Response::unauthorized(['message' => 'Token has expired']);
        } else {
            $user = User::getById($sessionData->id);
            $request->attributes->add(['user' => $user]);
        }

        return $next($request);
    }
}
