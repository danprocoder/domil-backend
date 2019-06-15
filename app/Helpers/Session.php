<?php
namespace App\Helpers;

use Carbon\Carbon;
use App\Session as SessionModel;

class Session
{
    static function create($request, $userId)
    {
        $sessionToken = 'auth-'.md5($userId.microtime());

        $data = [
            'session_id' => md5($sessionToken),
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->server('HTTP_USER_AGENT'),
            'expires' => Carbon::now()->addDays(5)
        ];
        SessionModel::create($data);

        return $sessionToken;
    }
    
    static function verify($token)
    {
        return SessionModel::find(md5($token));
    }
}
