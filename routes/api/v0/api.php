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
    /** contact us */
    Route::post('contact/us', 'api\v0\LoginController@contact_us');
    /** register clients */
    Route::prefix('/clients')->group( function(){
        Route::post('/register', 'api\v0\user\UserController@create_client');
    });
    /** register partners */
    Route::prefix('/partners')->group( function(){
        Route::post('/register', 'api\v0\user\UserController@create_partner');
        Route::post('/become/request', 'api\v0\user\UserController@become_partner');
        Route::get('/info/topic/{t}', 'api\v0\user\UserinfoController@show_bytopic');
        Route::get('/info/all', 'api\v0\user\UserinfoController@show_ptn_all');
    });
    Route::get('/findbyid/active/{id}', 'api\v0\user\UserController@show_active');
    /** =======AUTHENTICATED ROUTES======================================= */
    // Route::middleware('auth:api', 'verified')->
    Route::middleware('auth:api')->group( function(){
        /** is logged in  */
        Route::get('/loginstatus', 'api\v0\LoginController@is_active');
        /** shipping */
        Route::get('/shipping/user/{idf}', 'api\v0\user\ShippingController@show');
        Route::put('/shipping/user/{idf}', 'api\v0\user\ShippingController@update');
        Route::post('/shipping/user/{idf}', 'api\v0\user\ShippingController@create');
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
    /** UNSECURED */
    /** prices */
    Route::prefix('/prices')->group( function(){
        Route::get('/prices', 'api\v0\happybox\PriceController@index');
        Route::get('/price/{id}', 'api\v0\happybox\PriceController@show');
        Route::get('/price/byidf/{id}', 'api\v0\happybox\PriceController@show_byidf');
    });
    /** topics */
    Route::prefix('/topics')->group( function(){
        Route::get('/topics', 'api\v0\happybox\TopicController@index');
        Route::get('/topic/{id}', 'api\v0\happybox\TopicController@show');
        Route::get('/topic/byidf/{id}', 'api\v0\happybox\TopicController@show_byidf');
        Route::get('/topic/name/{n}', 'api\v0\happybox\TopicController@show_byname');
    });
    /** boxes */
    Route::prefix('/happyboxes')->group( function(){
        Route::get('/happyboxes', 'api\v0\happybox\HappyBoxController@index');
        Route::get('/happyboxes/active', 'api\v0\happybox\HappyBoxController@index_active');
        Route::get('/happyboxes/topic/{t}', 'api\v0\happybox\HappyBoxController@index_bytopic');
        Route::get('/happybox/{id}', 'api\v0\happybox\HappyBoxController@show');
        Route::get('/happybox/byidf/{box_internal_id}', 'api\v0\happybox\HappyBoxController@byidf');
    });
    /** pictures */
    Route::prefix('/pictures')->group( function(){
        Route::get('/pictures', 'api\v0\happybox\PictureController@index');
        Route::get('/picture/{id}', 'api\v0\happybox\PictureController@show');
        Route::get('/picture/byitem/{item}', 'api\v0\happybox\PictureController@byitem');
        Route::get('/picture/byitem/single/{item}', 'api\v0\happybox\PictureController@byitem_one');
    });
    /** inventories */
    Route::prefix('/inventories')->group( function(){
        Route::get('/inventory/ibarcode/{barcode}/type/{type}', 'api\v0\happybox\InventoryController@bcode');
        Route::get('/inventory/stock/{box}', 'api\v0\happybox\InventoryController@stock');
    });
    Route::prefix('/orders')->group( function(){
        Route::get('/order/req/id/{id}', 'api\v0\happybox\OrderController@findby_check_out_Req');
    });
    Route::prefix('/orders')->group( function(){
        Route::get('/order/ord/id/{id}', 'api\v0\happybox\OrderController@findby_ord_Req');
    });
    /** ratings */
    Route::prefix('/ratings')->group( function(){
        Route::get('/ratings', 'api\v0\happybox\RatingController@index');
        Route::get('/ratings/partner/{idf}', 'api\v0\happybox\RatingController@by_ptn_value');
        Route::get('/ratings/ptn/{idf}', 'api\v0\happybox\RatingController@by_ptn');
    });
    /** SECURED */
    Route::middleware('auth:api')->group( function(){
        /** ratings */
        Route::prefix('/ratings')->group( function(){
            Route::post('/ratings', 'api\v0\happybox\RatingController@create');
        });
        /** orders */
        Route::prefix('/orders')->group( function(){
            Route::get('/orders', 'api\v0\happybox\OrderController@index');
            Route::get('/order/{id}', 'api\v0\happybox\OrderController@show');
            Route::get('/order/ex/{ord}', 'api\v0\happybox\OrderController@show_ex');
            Route::get('/order/order/lmt/{order}', 'api\v0\happybox\OrderController@by_order_limited');
            Route::get('/order/order/{order}', 'api\v0\happybox\OrderController@by_order');
            Route::get('/order/customer/{customer}', 'api\v0\happybox\OrderController@by_customer');
            Route::put('/order/pay/true/{order}', 'api\v0\happybox\OrderController@mark_paid_success');
            Route::put('/order/checkout/reqid/{order}', 'api\v0\happybox\OrderController@check_out_Req');
            Route::put('/order/pay/false/{order}', 'api\v0\happybox\OrderController@mark_paid_fail');
            Route::put('/order/shipment/{order}', 'api\v0\happybox\OrderController@update_shipping');
            Route::post('/order', 'api\v0\happybox\OrderController@create');
            Route::post('/order/add/a/pay', 'api\v0\happybox\OrderController@record_a_payment');
            Route::post('/order/create/order/evouchers','api\v0\happybox\InventoryController@create_c_buyer_ebox');
            Route::post('/order/assign/order/pvouchers','api\v0\happybox\InventoryController@assign_c_buyer_pbox');
            Route::post('/order/find/vouchers','api\v0\happybox\InventoryController@find_o_voucher');
            Route::post('/order/mail/evouchers','api\v0\happybox\OrderController@mail_e_voucher');
            Route::post('/order/mail/fullorder','api\v0\happybox\OrderController@mail_fullorder');
            Route::post('/new/cron','api\v0\happybox\OrderController@new_cron');
            Route::post('/run/cron','api\v0\happybox\OrderController@run_cron');
            //
        });
        /** prices */
        Route::prefix('/prices')->group( function(){
            Route::post('/price', 'api\v0\happybox\PriceController@create');
            Route::put('/price/{id}', 'api\v0\happybox\PriceController@update');
        });
        /** topics */
        Route::prefix('/topics')->group( function(){
            Route::post('/topic', 'api\v0\happybox\TopicController@create');
            Route::put('/topic/{id}', 'api\v0\happybox\TopicController@update');
        });
        /** media */
        Route::prefix('/pictures')->group( function(){
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
            Route::get('/inventory/v/{v}', 'api\v0\happybox\InventoryController@by_voucher');
            Route::get('/inventory/ptn/{p}', 'api\v0\happybox\InventoryController@by_partner');
            Route::get('/inventory/cu/{c}', 'api\v0\happybox\InventoryController@by_cust_user');
            Route::post('/inventory', 'api\v0\happybox\InventoryController@create');
            Route::post('/inventory/reports', 'api\v0\happybox\InventoryController@get_report');
            Route::post('/inventory/ptn/pay/effec/date/{id}', 'api\v0\happybox\InventoryController@ptn_pay_effec_dt');
            Route::put('/inventory/redeem/bypartner/{v}', 'api\v0\happybox\InventoryController@redeem_by_partner');
            Route::put('/inventory/modify/booking/{v}', 'api\v0\happybox\InventoryController@modify_booking');
            Route::put('/inventory/cancel/ptn/voucher/{v}', 'api\v0\happybox\InventoryController@cancel_booking');
            Route::put('/inventory/activate/cu/{v}', 'api\v0\happybox\InventoryController@v_activate');
            Route::put('/inventory/cancel/voucher/{v}', 'api\v0\happybox\InventoryController@v_cancel');
            Route::get('/inventory/barcode/{barcode}/type/{type}', 'api\v0\happybox\InventoryController@bcode');
            Route::post('/inventory/barcode/vouchers', 'api\v0\happybox\InventoryController@bcodev');
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
            // Route::get('/happyboxes', 'api\v0\happybox\HappyBoxController@index');
            // Route::get('/happybox/{id}', 'api\v0\happybox\HappyBoxController@show');
            // Route::get('/happybox/byidf/{box_internal_id}', 'api\v0\happybox\HappyBoxController@byidf');
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
