<?php

namespace App\Http\Controllers\api\v0;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\User;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;


    public function sendResetLinkResponse( Request $request, $response ){
        return response([
            'status' => 0,
            'message' => $response
        ], 200);
    }

    public function sendResetLinkFailedResponse( Request $request, $response ){
        return response([
            'status' => -211,
            'message' => $response
        ], 422);
    }
}
