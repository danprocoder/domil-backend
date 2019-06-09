<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sms;
use App\ActivityLog;

class EditProfileController extends Controller
{
    function validator($data)
    {
        $nameRegex = '/^[a-zA-Z]+$/';

        return Validator::make($data, [
            'firstname' => ['nullable', 'regex:'.$nameRegex],
            'lastname' => ['nullable', 'regex:'.$nameRegex],
            'email' => ['nullable', 'email', 'unique:users'],
            'mobile' => ['nullable', 'regex:/^\+234[0-9]{10}$/', 'unique:users']
        ], [
            'firstname.regex' => 'Firstname can only contain letters',
            'lastname.regex' => 'Lastname can only contain letters',
            'email.email' => 'Please provide a valid email',
            'email.unique' => 'Email address already used by another user',
            'mobile.regex' => 'Mobile number format is not valid',
            'mobile.unique' => 'Mobile number already used by another user'
        ]);
    }

    function update(Request $request)
    {
        $user = $request->get('user');

        $userInput = $request->all();
        if (isset($userInput['email']) && $userInput['email'] == $user->email) {
            unset($userInput['email']);
        }
        if (isset($userInput['mobile']) && $userInput['mobile'] == $user->mobile) {
            unset($userInput['mobile']);
        }

        $validator = $this->validator($userInput);

        if ($validator->fails()) {
            return Response::error([
                'errors' => $validator->errors()
            ]);
        } else {
            $updateData = [];
            $updatedFields = []; // For activity log

            foreach (['firstname', 'lastname', 'email', 'mobile'] as $k) {
                if (!empty($userInput[$k])) {
                    $updateData[$k] = $userInput[$k];

                    $updatedFields[] = $k;
                }

                // Generate a new verification code for the user if the user changed his/her mobile number.
                if ($k == 'mobile' && isset($updateData['mobile'])) {
                    $updateData['mobile_verification_code'] = rand(100000, 999999);
                    $updateData['mobile_verified_at'] = null;
                }
            }

            $user->update($updateData);

            // Log user's activity when they update their profile
            if (count($updatedFields) > 0) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'activity_type' => 'user.profile_update',
                    'note' => 'Updated fields: '.implode(', ', $updatedFields)
                ]);
            }

            // Send new verification code to user if the user updated his/her mobile number.
            if (isset($updateData['mobile'])) {
                Sms::sendMessage($user->mobile, $user->mobile_verification_code);
            }

            return Response::success([
                'message' => 'User details updated successfully',
                'user' => $user
            ]);
        }
    }
}
