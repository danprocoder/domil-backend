<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $fillable = [
        'customer_id',
        'brand_id',
        'total_amount',
        'brand_share',
        'company_share',
        'meta_for',
        'meta_id',
        'payment_ref',
        'paid_brand_at',
    ];
}
