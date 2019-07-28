<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';
    
    protected $fillable = [
        'session_id',
        'user_id',
        'activity_type',
        'meta_id',
        'user_agent',
        'ip',
        'note'
    ];
}
