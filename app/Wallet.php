<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallet';

    protected $fillable = [
        'user_id',
        'brand_id',
        'type',
        'amount',
        'balance'
    ];

    static function getUserCurrentBalance($userId)
    {
        $row = self::where('user_id', $userId)->orderBy('id', 'DESC')->first();
        return $row ? $row->balance : 0;
    }

    const TRANSACTION_TYPE_CREDIT = 'credit';
}
