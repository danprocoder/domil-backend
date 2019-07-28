<?php

namespace App\Http\Middleware\User;

use Closure;
use App\Helpers\Response;

class CheckEmailVerified
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
        $user = $request->get('user');

        if (!$user->email_verified_at) {
            return Response::forbidden([
                'message' => 'Email address not verified',
            ]);
        }

        return $next($request);
    }
}
