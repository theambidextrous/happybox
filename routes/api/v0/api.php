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
        /** is logged in  */
        Route::get('/loginstatus', 'api\v0\LoginController@is_active');
        /** userinfo */
        Route::get('/info/{userid}', 'api\v0\user\UserinfoController@show');
        Route::get('/info/byidf/{userid}', 'api\v0\user\UserinfoController@show_byidf');
        /** upload profile pic */
        Route::post('/profilepic/{userid}', 'api\v0\user\UserinfoController@change_profile');
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
            Route::post('/profile/{id}', 'api\v0\user\UserinfoController@create_for_client');
            Route::put('/profile/{userid}', 'api\v0\user\UserinfoController@update_client');
        });
        /** find partners */
        Route::prefix('/partners')->group( function(){
            Route::get('/findall', 'api\v0\user\UserController@partners');
            Route::post('/profile/{id}', 'api\v0\user\UserinfoController@create_for_partner');
            Route::put('/profile/{userid}', 'api\v0\user\UserinfoController@update_partner');
        });
        /** find admins */
        Route::prefix('/admins')->group( function(){
            Route::get('/findall', 'api\v0\user\UserController@admins');
            Route::post('/profile/{id}', 'api\v0\user\UserinfoController@create_for_admin');
            Route::put('/profile/{userid}', 'api\v0\user\UserinfoController@update_admin');
        });
    });
    /** RESETS */
    Route::post('/forgotpassword', 'api\v0\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/resetpassword', 'api\v0\ResetPasswordController@reset');
    /** VERIFY */
    Route::get('/email/resend', 'api\v0\VerificationController@resend')->name('verification.resend');
    Route::get('/email/verify/{id}/{hash}', 'api\v0\VerificationController@verify')->name('verification.verify');
}); 

/** Services */
Route::prefix('/services')->group( function() {
    Route::middleware('auth:api')->group( function(){
        /** topics */
        Route::prefix('/topics')->group( function(){
            Route::get('/topics', 'api\v0\happybox\TopicController@index');
            Route::get('/topic/{id}', 'api\v0\happybox\TopicController@show');
            Route::get('/topic/byidf/{id}', 'api\v0\happybox\TopicController@show_byidf');
            Route::post('/topic', 'api\v0\happybox\TopicController@create');
            Route::put('/topic/{id}', 'api\v0\happybox\TopicController@update');
        });
        /** media */
        Route::prefix('/pictures')->group( function(){
            Route::get('/pictures', 'api\v0\happybox\PictureController@index');
            Route::get('/picture/{id}', 'api\v0\happybox\PictureController@show');
            Route::get('/picture/byitem/{item}', 'api\v0\happybox\PictureController@byitem');
            Route::post('/picture/{item}/type/{type}', 'api\v0\happybox\PictureController@create');
            Route::post('/picture/{id}', 'api\v0\happybox\PictureController@update');
        });
        /** media types*/
        Route::prefix('/mediatypes')->group( function(){
            Route::get('/mediatypes', 'api\v0\happybox\MediatypeController@index');
            Route::get('/mediatype/{id}', 'api\v0\happybox\MediatypeController@show');
            Route::post('/mediatype', 'api\v0\happybox\MediatypeController@create');
            Route::put('/mediatype/{id}', 'api\v0\happybox\MediatypeController@update');
        });
        /** experiences */
        Route::prefix('/experiences')->group( function(){
            Route::get('/experiences', 'api\v0\happybox\ExperienceController@index');
            Route::get('/experience/{id}', 'api\v0\happybox\ExperienceController@show');
            Route::get('/experience/byidf/{internal_id}', 'api\v0\happybox\ExperienceController@byidf');
            Route::get('/experience/bytopic/{topic_internal_id}', 'api\v0\happybox\ExperienceController@bytopic');
            Route::get('/experience/bypartner/{partner_internal_id}', 'api\v0\happybox\ExperienceController@bypartner');
            Route::post('/experience', 'api\v0\happybox\ExperienceController@create');
            Route::put('/experience/{id}', 'api\v0\happybox\ExperienceController@update');
        });
        /** happyboxexperiences */
        Route::prefix('/happyboxexperiences')->group( function(){
            Route::get('/happyboxexperiences', 'api\v0\happybox\HappyBoxExperienceController@index');
            Route::get('/happyboxexperience/{id}', 'api\v0\happybox\HappyBoxExperienceController@show');
            Route::get('/happyboxexperience/byexperience/{experience_internal_id}', 'api\v0\happybox\HappyBoxExperienceController@byexperience');
            Route::get('/happyboxexperience/bybox/{box_internal_id}', 'api\v0\happybox\HappyBoxExperienceController@bybox');
            Route::post('/happyboxexperience', 'api\v0\happybox\HappyBoxExperienceController@create');
            Route::put('/happyboxexperience/{id}', 'api\v0\happybox\HappyBoxExperienceController@update');
            Route::post('/happyboxexperience/delete', 'api\v0\happybox\HappyBoxExperienceController@destroy');
        });

        /** inventories */
        Route::prefix('/inventories')->group( function(){
            Route::get('/inventory', 'api\v0\happybox\InventoryController@index');
            Route::get('/inventory/{id}', 'api\v0\happybox\InventoryController@show');
            Route::get('/inventory/vstatus/{status}', 'api\v0\happybox\InventoryController@by_voucher_status');
            Route::post('/inventory', 'api\v0\happybox\InventoryController@create');
        });
         /** reports */
         Route::prefix('/reports')->group( function(){
            Route::get('/report', 'api\v0\happybox\ReportController@index');
            Route::get('/report/{id}', 'api\v0\happybox\ReportController@show');
            Route::post('/report', 'api\v0\happybox\ReportController@create');
            Route::put('/report/{id}', 'api\v0\happybox\ReportController@update');
        });
        /** happyboxes */
        Route::prefix('/happyboxes')->group( function(){
            Route::get('/happyboxes', 'api\v0\happybox\HappyBoxController@index');
            Route::get('/happybox/{id}', 'api\v0\happybox\HappyBoxController@show');
            Route::get('/happybox/byidf/{box_internal_id}', 'api\v0\happybox\HappyBoxController@byidf');
            Route::post('/happybox', 'api\v0\happybox\HappyBoxController@create');
            Route::put('/happybox/activate/{id}', 'api\v0\happybox\HappyBoxController@activate');
            Route::put('/happybox/deactivate/{id}', 'api\v0\happybox\HappyBoxController@deactivate');
            Route::put('/happybox/{id}', 'api\v0\happybox\HappyBoxController@update');
        });
    });
});
Route::fallback(function(){
    return response()->json([
        'status' => -211,
        'message' => 'resource not found'], 404);
});
