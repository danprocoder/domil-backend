<?php

namespace App\Http\Controllers\Job;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Brand;
use App\User;
use App\Job;
use App\ActivityLog;

class JobController extends Controller
{
    function postValidator($data)
    {
        return Validator::make($data, [
            'title' => 'required',
            'description' => 'required'
        ], [
            'title.required' => 'Job title is required',
            'description.required' => 'Job description is required'
        ]);
    }

    function create(Request $request, $brandId)
    {
        $loggedInUser = $request->get('user');

        $brand = Brand::getById($brandId);
        if (!$brand) {
            return Response::notFound(['message' => 'Brand does not exists']);
        }
        // A user can't post a job to himself.
        if ($brand->user_id == $loggedInUser->id) {
            return Response::error(['message' => 'An error occurred']);
        }

        $inputs = $request->all();

        $validator = $this->postValidator($inputs);
        if ($validator->fails()) {
            return Response::error([
                'errors' => $validator->errors(),
            ]);
        }

        $job = Job::create([
            'user_id' => $loggedInUser->id,
            'brand_id' => $brand->id,
            'title' => $inputs['title'],
            'description' => $inputs['description']
        ]);

        // Log user's activity
        ActivityLog::create([
            'user_id' => $loggedInUser->id,
            'activity_type' => 'job.create',
            'meta_id' => $job->id
        ]);

        return Response::success([
            'message' => 'Job posted to '.$brand->name.' successfully',
            'brand' => $brand,
            'job' => $job
        ]);
    }
}
