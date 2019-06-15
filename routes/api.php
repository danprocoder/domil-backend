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
Route::get('/user/mobile-verification-code/resend', 'Auth\MobileVerificationController@resendCode')->middleware('check_api_token');
Route::patch('/user', 'Auth\EditProfileController@update')->middleware('check_api_token');

Route::post('/brand', 'Brand\BrandController@create')->middleware('check_api_token');
Route::patch('/brand', 'Brand\BrandController@update')->middleware('check_api_token');

Route::post('/brand/{brand_id}/job', 'Job\JobController@create')->middleware('check_api_token');
Route::get('/brand/jobs', 'Job\JobController@getBrandJobs')->middleware('check_api_token');
Route::patch('/brand/job/{id}/price', 'Job\JobPaymentController@setPrice')->middleware('check_api_token');
Route::post('/brand/{id}/portfolio', 'Brand\BrandPortfolioController@create')->middleware('check_api_token');
Route::delete('/brand/{id}/portfolio/{item_id}', 'Brand\BrandPortfolioController@removeOne')->middleware("check_api_token");

Route::get('/customer/jobs', 'Job\JobController@getCustomerJobs')->middleware('check_api_token');
Route::patch('/customer/job/{id}/verify-payment', 'Job\JobPaymentController@verifyPayment')->middleware('check_api_token');

Route::get('/{acting_as}/job/{id}', 'Job\JobController@getOne')->where('acting_as', '^(brand|customer)$')->middleware('check_api_token');
