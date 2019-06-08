<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'about',
        'logo_url',
        'verified_at'
    ];

    static function getByUserId($userId)
    {
        return self::where('user_id', $userId)->first();
    }
}
