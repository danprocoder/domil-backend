<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Helpers\Response;

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
            return Response::success(['message' => 'It will verify now']);
        }
    }
}
