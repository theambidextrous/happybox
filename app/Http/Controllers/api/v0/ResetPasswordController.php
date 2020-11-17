<?php

namespace App\Http\Controllers\api\v0;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;


    protected function sendResetResponse( Request $request, $response ){
        return response(['status' => 0,'message' => trans($response)]);
    }

    protected function sendResetFailedResponse( Request $request, $response ){
        return response(['status' => -211,'message' => trans($response)]);
    }
}

