<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/user/create', 'Auth\RegisterController@create');
Route::post('/user/auth', 'Auth\LoginController@authenticate');
Route::get('/user/verify/mobile', 'Auth\MobileVerificationController@verifyCode')->middleware('check_api_token');
