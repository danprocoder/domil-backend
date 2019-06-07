<?php

namespace App\Helpers;

class Sms
{
    static function sendMessage($recipient, $message)
    {
        \Log::info("Sent '$message' to '$recipient'");
    }
}
