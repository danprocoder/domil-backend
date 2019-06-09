<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobAttachment extends Model
{
    protected $table = 'jobs_attachments';

    protected $fillable = ['job_id', 'file_url', 'created_at', 'updated_at'];

    static function addAll($jobId, $fileUrls)
    {
        $rows = [];
        foreach ($fileUrls as $url) {
            $rows[] = [
                'job_id' => $jobId,
                'file_url' => $url,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        self::insert($rows);
    }
}
