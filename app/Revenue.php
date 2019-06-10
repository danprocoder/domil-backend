<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $table = 'revenue';

    protected $fillable = ['amount', 'balance'];

    static function getCurrentBalance()
    {
        $lastRow = self::orderBy('id', 'DESC')->first();
        return $lastRow ? $lastRow->balance : 0;
    }
}
