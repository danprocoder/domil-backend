<?php

namespace App\Http\Controllers\Job;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yabacon\Paystack;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Job;
use App\Brand;
use App\ActivityLog;
use App\CustomerPayment;
use App\Revenue;

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

    function verifyPayment(Request $request, $jobId)
    {
        $loggedInUser = $request->get('user');

        $job = Job::getById($jobId);
        if (empty($job)) {
            return Response::notFound(['message' => 'Job does not exists']);
        }

        if ($loggedInUser->id != $job->user_id) {
            return Response::forbidden(['message' => 'Access forbidden']);
        }

        // Payment can only be verified once to avoid creditting brands multiple times.
        if (!empty($job->paid_at)) {
            return Response::error(['message' => 'Payment already verified']);
        }

        $paystack = new Paystack(config('paystack.secret_key'));
        try {
            $tranx = $paystack->transaction->verify([
                'reference' => $job->payment_ref
            ]);
        } catch (Paystack\Exception\ApiException $e) {
            return Response::error([
                'error' => $e->getResponseObject()
            ]);
        }

        if ($tranx->data->status === 'success') {
            $amountPaid = $tranx->data->amount;

            if ($amountPaid != $job->price) {
                return Response::error(['message' => 'The right amount was not paid']);
            }

            /*
             * Insert into customer payments
             */
            $brand = Brand::getById($job->brand_id);
            $brandShare = floor((80 / 100) * $amountPaid);
            $companyShare = $amountPaid - $brandShare;

            $customerPaymentId = CustomerPayment::create([
                'customer_id' => $loggedInUser->id,
                'brand_id' => $brand->id,
                'total_amount' => $amountPaid,
                'brand_share' => $brandShare,
                'company_share' => $companyShare,
                'meta_for' => 'job',
                'meta_id' => $job->id,
                'payment_ref' => $job->payment_ref
            ])->id;
            
            // Update job record.
            $job->update([
                'paid_at' => Carbon::parse($tranx->data->paid_at),
                'customer_payment_id' => $customerPaymentId,
                'current_status' => 'in-progress'
            ]);

            // Log user's activity
            ActivityLog::create([
                'user_id' => $loggedInUser->id,
                'activity_type' => 'customer.job.paid',
                'meta_id' => $job->id
            ]);

            return Response::success([
                'message' => 'Payment verified successfully',
                'job' => $job
            ]);
        } else {
            return Response::error([
                'tranx' => $tranx
            ]);
        }
    }
}
