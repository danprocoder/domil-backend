<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\Response;
use App\Helpers\Session;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request)
    {
        $user = User::getByEmail($request->input('email'));

        if (!$user) {
            return Response::error([
                'error' => 'Incorrect email',
            ]);
        } else {
            if (!Hash::check($request->input('password'), $user->password)) {
                return Response::error([
                    'error' => 'Incorrect password'
                ]);
            } else {
                $sessionToken = Session::create(['id' => $user->id]);
                return Response::success([
                    'token' => $sessionToken,
                    'user' => [
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'email' => $user->email,
                        'mobile' => $user->mobile,
                        'email_verified' => $user->email_verified_at != null,
                        'mobile_verified' => $user->mobile_verified_at != null
                    ],
                ]);
            }
        }
    }
}
