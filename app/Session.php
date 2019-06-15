<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'expires'
    ];

    protected $primaryKey = 'session_id';
}
