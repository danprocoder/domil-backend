<?php
namespace App\Helpers;

use Carbon\Carbon;

class Session
{
    static function create($data)
    {
        $data['expires'] = Carbon::now()->addDays(5);

        return base64_encode(json_encode($data));
    }
    
    static function verify($token)
    {
        $data = json_decode(base64_decode($token));
        return $data;
    }
}
