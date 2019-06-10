<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'user_id',
        'brand_id',
        'title',
        'description',
        'price',
        'payment_ref',
        'price_set_at'
    ];

    static function getCustomerJobs($customerId)
    {
        return self::where('user_id', $customerId)->orderBy('id', 'DESC')->get();
    }

    static function getBrandJobs($brandId)
    {
        return self::where('brand_id', $brandId)->orderBy('id', 'DESC')->get();
    }

    static function getById($jobId)
    {
        return self::where('id', $jobId)->first();
    }
}
