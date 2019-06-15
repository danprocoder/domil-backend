<?php

namespace App\Http\Controllers\Brand;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Rules\UploadedImageUrl;
use App\Helpers\Response;
use App\Helpers\ActivityLog;
use App\Brand;
use App\BrandPortfolio;

class BrandPortfolioController extends Controller
{
    function getValidator($input)
    {
        $rules = [
            'item' => 'required|array',
            'item.*.image_url' => ['required', new UploadedImageUrl],
            'item.*.caption' => 'nullable|max:200'
        ];

        $messages = [
            'item.required' => 'Image URL is required',
            'item.array' => 'Image URL must be an array',
            'item.*.image_url.required' => 'Image URL is required',
            'item.*.caption.max' => 'Caption should not exceed 200 characters'
        ];
        
        return Validator::make($input, $rules, $messages);
    }

    function create(Request $request, $brandId)
    {
        $loggedInUser = $request->get('user');

        // User must be the owner of the brand.
        if (!Brand::userHasBrand($loggedInUser->id, $brandId)) {
            return Response::forbidden([
                'message' => 'Access forbidden'
            ]);
        }

        $input = $request->all();
        $validator = $this->getValidator($input);
        if ($validator->fails()) {
            return Response::error([
                'message' => 'Could not add items to portfolio',
                'errors' => $validator->errors()
            ]);
        }

        // Add images to portfolio
        BrandPortfolio::addItems($brandId, $input['item']);

        // Log user's activity
        ActivityLog::log($request, 'brand.portfolio.add', $brandId);

        return Response::success([
            'message' => 'Items successfully added to portfolio',
            'items_added' => count($input['item']),
        ]);
    }

    function removeOne(Request $request, $brandId, $itemId)
    {
        $loggedInUser = $request->get('user');

        // User must be the owner of the brand
        if (!Brand::userHasBrand($loggedInUser->id, $brandId)) {
            return Response::forbidden([
                'message' => 'Access forbidden'
            ]);
        }

        $item = BrandPortfolio::getBrandItem($brandId, $itemId);
        if (!$item) {
            return Response::notFound([
                'message' => 'Portfolio item was not found'
            ]);
        }

        // Delete the item.
        $item->delete();

        // Log user's activity
        ActivityLog::log($request, 'brand.portfolio.delete', $brandId);

        return Response::success([
            'message' => 'Portfolio item deleted successfully',
        ]);
    }
}
