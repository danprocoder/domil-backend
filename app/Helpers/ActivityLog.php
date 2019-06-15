<?php

namespace App\Helpers;

class ActivityLog
{
    public static function log($request, $activityType, $metaId=null, $note=null)
    {
        $userId = $request->get('user')->id;
        $sessionToken = $request->header('Session-Token');
        
        return \App\ActivityLog::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'meta_id' => $metaId,
            'note' => $note,
            'session_id' => md5($sessionToken)
        ]);
    }
}
