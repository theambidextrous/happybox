<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/** Users */
Route::prefix('/users')->group( function() {
    /** all users login */
    Route::post('/login', 'api\v0\LoginController@login');
    /** register clients */
    Route::prefix('/clients')->group( function(){
        Route::post('/register', 'api\v0\user\UserController@create_client');
    });
    /** register partners */
    Route::prefix('/partners')->group( function(){
        Route::post('/register', 'api\v0\user\UserController@create_partner');
    });
    /** =======AUTHENTICATED ROUTES======================================= */
    // Route::middleware('auth:api', 'verified')->
    Route::middleware('auth:api')->group( function(){
        /** register admins --- using root user */
        Route::prefix('/admins')->group( function(){
            Route::post('/register', 'api\v0\user\UserController@create_admin');
        });
        /** find user(s) */
        Route::get('/findall', 'api\v0\user\UserController@all');
        Route::get('/findbyid/{id}', 'api\v0\user\UserController@show');
        Route::get('/findbyemail/{email}', 'api\v0\user\UserController@showbyemail');
        /** find clients */
        Route::prefix('/clients')->group( function(){
            Route::get('/findall', 'api\v0\user\UserController@clients');
        });
        /** find partners */
        Route::prefix('/partners')->group( function(){
            Route::get('/findall', 'api\v0\user\UserController@partners');
        });
        /** find admins */
        Route::prefix('/admins')->group( function(){
            Route::get('/findall', 'api\v0\user\UserController@admins');
        });
    });
    /** RESETS */
    Route::post('/forgotpassword', 'api\v0\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/resetpassword', 'api\v0\ResetPasswordController@reset');
    /** VERIFY */
    Route::get('/email/resend', 'api\v0\VerificationController@resend')->name('verification.resend');
    Route::get('/email/verify/{id}/{hash}', 'api\v0\VerificationController@verify')->name('verification.verify');
}); 
Route::fallback(function(){
    return response()->json([
        'status' => -211,
        'message' => 'resource not found'], 404);
});
