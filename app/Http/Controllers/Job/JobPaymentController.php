<?php

namespace App\Http\Controllers\Job;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Job;
use App\Brand;
use App\ActivityLog;

class JobPaymentController extends Controller
{
    function getPriceValidator($data)
    {
        return Validator::make($data, [
            'price' => 'required|regex:/^[0-9]+(\.[0-9]{0,2})?$/',
        ], [
            'price.required' => 'Price is required',
            'price.regex' => 'Invalid price'
        ]);
    }

    function setPrice(Request $request, $jobId)
    {
        $job = Job::getById($jobId);
        if (!$job) {
            return Response::notFound(['message' => 'Job does not exists']);
        }

        $loggedInUser = $request->get('user');

        if (!Brand::userHasBrand($loggedInUser->id, $job->brand_id)) {
            return Response::forbidden(['message' => 'Access forbidden']);
        }

        // Price cannot be set after user has paid.
        if (!empty($job->paid_at)) {
            return Response::error(['message' => 'Price cannot be set after payment has been made']);
        }

        // Price cannot be set twice
        if (!empty($job->price)) {
            return Response::error(['message' => 'Price has already been set']);
        }

        $data = $request->all();
        $validator = $this->getPriceValidator($data);
        if ($validator->fails()) {
            return Response::error(['errors' => $validator->errors()]);
        }

        $price = (int) ($data['price'] * 100);
        $payment_ref = 'job-'.md5($job->id.microtime());
        $job->update([
            'price' => $price,
            'payment_ref' => $payment_ref,
            'price_set_at' => Carbon::now()
        ]);
        
        // Log user's activity.
        ActivityLog::create([
            'user_id' => $loggedInUser->id,
            'activity_type' => 'brand.job.price_set',
            'meta_id' => $job->id,
            'note' => 'Price: NGN'.$price
        ]);

        return Response::success([
            'message' => 'Price for job set successfully',
            'job' => $job
        ]);
    }
}
