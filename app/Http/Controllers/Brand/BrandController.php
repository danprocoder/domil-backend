<?php

namespace App\Http\Controllers\Brand;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Brand;
use App\ActivityLog;

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

            ActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'brand.create',
                'meta_id' => $brand->id
            ]);

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

        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'brand.update',
            'meta_id' => $brand->id,
            'note' => 'Updated fields: '.implode(', ', $updatedFields)
        ]);

        return Response::success([
            'message' => 'User brand details updated successfully',
            'brand' => $brand
        ]);
    }
}
