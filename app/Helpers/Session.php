<?php
namespace App\Helpers;

class Session
{
    static function create($data)
    {
        return base64_encode(json_encode($data));
    }
    
    static function verify($token)
    {
        return json_decode(base64_decode($token));
    }
}
