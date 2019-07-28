<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Helpers\ActivityLog;
use App\User;

class EmailVerificationController extends Controller
{
    function verify(Request $request)
    {
        $email = $request->get('email');
        $activationCode = $request->get('activation_code');

        if (!$email || !$activationCode) {
            return Response::badRequest([
                'message' => 'Bad request',
            ]);
        }

        $user = User::getByEmail($email);
        if (empty($user)) {
            return Response::unauthorized([
                'message' => 'Email address not registered',
            ]);
        }

        if (!empty($user->email_verified_at)) {
            return Response::forbidden([
                'message' => 'Email address already verified',
            ]);
        } elseif ($user->email_verification_code != $activationCode) {
            return Response::unauthorized([
                'message' => 'Email verification code is invalid',
            ]);
        }

        $emailVerifiedAt = \Carbon\Carbon::now();
        $user->update([
            'email_verified_at' => $emailVerifiedAt,
        ]);

        // Log user activity
        $request->attributes->add(['user' => $user]);
        ActivityLog::log($request, 'user.email_verified');

        return Response::success([
            'message' => 'Email address verified successfully',
            'timestamp' => $emailVerifiedAt,
        ]);
    }
}
