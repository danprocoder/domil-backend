<?php
namespace App\Helpers;

class Response
{
    static function created($data, $headers=[])
    {
        return response()->json($data, 201)->withHeaders($headers);
    }

    static function success($data, $headers=[])
    {
        return response()->json($data, 200)->withHeaders($headers);
    }

    static function error($data, $headers=[])
    {
        return response()->json($data, 400)->withHeaders($headers);
    }

    static function unauthorized($data, $headers=[])
    {
        return response()->json($data, 401)->withHeaders($headers);
    }

    static function forbidden($data, $headers=[])
    {
        return response()->json($data, 403)->withHeaders($headers);
    }
}
