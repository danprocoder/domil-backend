<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BrandPortfolio extends Model
{
    protected $table = 'brands_portfolio';

    protected $fillable = ['brand_id', 'image_url', 'caption', 'created_at'];

    public static function addItems($brandId, $items)
    {
        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                'brand_id' => $brandId,
                'image_url' => $item['image_url'],
                'caption' => $item['caption'],
                'created_at' => Carbon::now()
            ];
        }
        return self::insert($rows);
    }

    public static function getBrandItem($brandId, $itemId)
    {
        return self::where('brand_id', $brandId)->where('id', $itemId)->first();
    }
}
