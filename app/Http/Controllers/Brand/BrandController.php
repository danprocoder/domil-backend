<?php

namespace App\Http\Controllers\Brand;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Brand;
use App\BrandPortfolio;
use App\Helpers\ActivityLog;

class BrandController extends Controller
{
    function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required',
            'address' => 'required',
            'about' => 'required',
            'logo' => ['nullable', 'regex:/^https:\/\//']
        ], [
            'name.required' => 'Your brand name is required',
            'address.required' => 'Your address is required',
            'about.required' => 'About field is required',
            'logo.regex' => 'Image URL is not valid'
        ]);
    }

    function create(Request $request)
    {
        $user = $request->get('user');

        // Check if user has already created a brand
        $brand = Brand::getByUserId($user->id);
        if ($brand) {
            return Response::error([
                'message' => 'User brand already created'
            ]);
        }

        $userInputs = $request->all();
        $validator = $this->validator($userInputs);
        if ($validator->fails()) {
            return Response::error([
                'errors' => $validator->errors(),
            ]);
        } else {
            $rowData = [
                'user_id' => $user->id,
                'name' => $userInputs['name'],
                'address' => $userInputs['address'],
                'about' => $userInputs['about']
            ];
            if (isset($userInputs['logo'])) {
                $rowData['logo_url'] = $userInputs['logo'];
            }
            $brand = Brand::create($rowData);

            // Log user's activity
            ActivityLog::log($request, 'brand.create', $brand->id);

            return Response::created([
                'message' => 'User brand created successfully',
                'brand' => $brand
            ]);
        }
    }

    function update(Request $request)
    {
        $user = $request->get('user');

        $brand = Brand::getByUserId($user->id);
        if (!$brand) {
            return Response::error([
                'message' => 'User has not created a brand'
            ]);
        }

        $inputs = $request->all();

        $validator = Validator::make($inputs, [
            'logo_url' => ['nullable', 'regex:/^https:\/\//']
        ], [
            'logo_url.regex' => 'Image URL is not valid'
        ]);
        if ($validator->fails()) {
            return Response::error([
                'errors' => $validator->errors()
            ]);
        }

        $updateData = [];
        $updatedFields = [];
        foreach (['name', 'address', 'about', 'logo_url'] as $k) {
            if (!empty($inputs[$k])) {
                $updateData[$k] = $inputs[$k];

                $updatedFields[] = $k;
            }
        }
        $brand->update($updateData);

        // Log user's activity
        ActivityLog::log($request, 'brand.update', $brand->id, 'Updated fields: '.implode(', ', $updatedFields));

        return Response::success([
            'message' => 'User brand details updated successfully',
            'brand' => $brand
        ]);
    }

    function getDetails(Request $request, $brandId)
    {
        $loggedInUser = $request->get('user');

        $brand = Brand::find($brandId);
        if (!$brand) {
            return Response::notFound([
                'message' => 'Brand does not exists'
            ]);
        }

        // Get 6 most recent items in portfolio
        $portfolio = BrandPortfolio::where('brand_id', $brandId)->orderBy('id', 'DESC')->limit(6)->get();

        // Logged user activity if user is logged in.
        if ($loggedInUser) {
            ActivityLog::log($request, 'brand.view', $brandId);
        }

        return Response::success([
            'brand' => $brand,
            'portfolio' => $portfolio,
            'by_current_user' => $loggedInUser && $loggedInUser->id == $brand->user_id,
        ]);
    }
}
