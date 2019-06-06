<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'mobile' => ['required', 'regex:/^(\+234)?[0-9]{10}$/', 'unique:users']
        ], [
            'email.reqiured' => 'Please provide an email address',
            'email.email' => 'Email address is not valid',
            'email.unique' => 'Email address already taken by another user',
            'password.required' => 'Password is required',
            'password.min' => 'Password should be atleast 8 characters',
            'mobile.required' => 'Mobile number is required',
            'mobile.unique' => 'Mobile number already used by another user',
            'mobile.regex' => 'Mobile number is not valid',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    function create(Request $request)
    {
        $data = $request->all();
        $validator = $this->validator($data);

        if ($validator->fails()) {
            return Response::error([
                'errors' => $validator->errors(),
            ]);
        } else {
            $data['mobile'] = $this->addCountryCode($data['mobile']);
            $email = strtolower($data['email']);
            $mobileVerCode = rand(100000, 999999);

            $user = User::create([
                'email' => $email,
                'email_verification_code' => $this->generateEmailVerificationCode(),
                'mobile' => $data['mobile'],
                'mobile_verification_code' => $mobileVerCode,
                'password' => Hash::make($data['password']),
            ]);
            
            // Send text message to user's mobile number.
            Sms::sendMessage($data['mobile'], $mobileVerCode);

            $token = Session::create([
                'user_id' => $user->id,
            ]);

            return Response::created([
                'token' => $token,
                'user' => [
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $email,
                    'mobile' => $user->mobile,
                    'email_verified' => false,
                    'mobile_verified' => false
                ],
            ]);
        }
    }

    function addCountryCode($mobile) {
        return '+234'.$mobile;
    }

    function generateEmailVerificationCode() {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-';
        $code = '';
        for ($i = 0; $i < 24; $i++) {
            $code .= $charset[rand(0, strlen($charset) - 1)];
        }
        return $code;
    }
}
