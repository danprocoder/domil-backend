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
use App\JobAttachment;

class JobController extends Controller
{
    function postValidator($data)
    {
        return Validator::make($data, [
            'title' => 'required',
            'description' => 'required',
            'attachment' => 'nullable|array',
            'attachment.*' => 'nullable|regex:/^https:\/\//'
        ], [
            'title.required' => 'Job title is required',
            'description.required' => 'Job description is required',
            'attachment.array' => 'Attachment field must be an array',
            'attachment.*.regex' => 'Job attachment URL is not valid'
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
        // Add attachments
        if (!empty($inputs['attachment'])) {
            JobAttachment::addAll($job->id, $inputs['attachment']);
        }

        // Log user's activity
        ActivityLog::create([
            'user_id' => $loggedInUser->id,
            'activity_type' => 'job.create',
            'meta_id' => $job->id
        ]);

        return Response::success([
            'message' => 'Job posted to '.$brand->name.' successfully',
        ]);
    }

    function getBrandJobs(Request $request)
    {
        $loggedInUser = $request->get('user');
        $brand = Brand::getByUserId($loggedInUser->id);
        if (!$brand) {
            return Response::error(['message' => 'You don\'t have a brand']);
        }

        ActivityLog::create(['user_id' => $loggedInUser->id, 'activity_type' => 'brand.jobs.view-requests']);

        $jobs = Job::getBrandJobs($brand->id);

        return Response::success([
            'jobs' => $jobs
        ]);
    }

    function getCustomerJobs(Request $request)
    {
        $loggedInUser = $request->get('user');

        ActivityLog::create(['user_id' => $loggedInUser->id, 'activity_type' => 'customer.jobs.view-posted']);
        
        $jobs = Job::getCustomerJobs($loggedInUser->id);

        return Response::success([
            'jobs' => $jobs
        ]);
    }

    function getOne(Request $request, $jobId)
    {
        $loggedInUser = $request->get('user');

        $job = Job::getById($jobId);
        if (empty($job)) {
            return Response::notFound(['message' => 'Requested job was not found']);
        }

        if ($loggedInUser->id != $job->user_id && !Brand::userHasBrand($loggedInUser->id, $job->brand_id)) {
            return Response::forbidden(['message' => 'Access forbidden']);
        }

        return Response::success([
            'job' => $job
        ]);
    }
}
