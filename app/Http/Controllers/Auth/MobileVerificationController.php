<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Helpers\Sms;
use App\ActivityLog;

class MobileVerificationController extends Controller
{
    function validator($data)
    {
        return Validator::make($data, [
            'code' => ['required', 'regex:/^[0-9]{6}$/']
        ], [
            'code.required' => 'Code is required',
            'code.regex' => 'Code is invalid'
        ]);
    }

    function verifyCode(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return Response::error(['message' => $validator->errors()]);
        } else {
            $user = $request->get('user');
            if ($user->mobile_verified_at != null) {
                return Response::error([
                    'message' => 'Mobile number already verified'
                ]);
            }

            $code = $request->input('code');

            if ($code == $user->mobile_verification_code) {
                $user->update([
                    'mobile_verified_at' => \Carbon\Carbon::now()
                ]);

                ActivityLog::create(['user_id' => $user->id, 'activity_type' => 'mobile_verification.success']);

                return Response::success([
                    'message' => 'Mobile number verified successfully'
                ]);
            } else {
                ActivityLog::create(['user_id' => $user->id, 'activity_type' => 'mobile_verification.incorrect_code']);
                
                return Response::error([
                    'message' => 'Mobile verification code is incorrect'
                ]);
            }
        }
    }

    function resendCode(Request $request)
    {
        $user = $request->get('user');

        if ($user->mobile_verified_at != null) {
            return Response::error([
                'message' => 'Mobile number already verified'
            ]);
        }

        $newCode = rand(100000, 999999);
        $user->update(['mobile_verification_code' => $newCode]);

        ActivityLog::create(['user_id' => $user->id, 'activity_type' => 'mobile_verification.new_code_request']);

        Sms::sendMessage($user->mobile, $newCode);

        return Response::success([
            'message' => 'New verification code sent successfully'
        ]);
    }
}
