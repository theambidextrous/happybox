<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/docs.tpl');
});
Route::get('/docs.tpl', function () {
    return view('welcome');
});
Route::get('/barcode.tpl', 'api\v0\happybox\BarcodeController@barcode');
