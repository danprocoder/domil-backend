<?php

namespace App\Http\Controllers\Brand;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Brand;

class BrandController extends Controller
{
    function validator($data)
    {
        return Validator::make($data, [
            'name' => 'required',
            'address' => 'required',
            'about' => 'required'
        ], [
            'name.required' => 'Your brand name is required',
            'address.required' => 'Your address is required',
            'about.required' => 'About field is required'
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
            Brand::create([
                'user_id' => $user->id,
                'name' => $userInputs['name'],
                'address' => $userInputs['address'],
                'about' => $userInputs['about']
            ]);

            return Response::created([
                'message' => 'User brand created successfully'
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

        $updateData = [];
        foreach (['name', 'address', 'about'] as $k) {
            if (isset($inputs[$k])) {
                $updateData[$k] = $inputs[$k];
            }
        }
        $brand->update($updateData);

        return Response::success([
            'message' => 'User brand details updated successfully',
            'brand' => $brand
        ]);
    }
}
