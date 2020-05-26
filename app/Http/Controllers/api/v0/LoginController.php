<?php

namespace App\Http\Controllers\api\v0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request ){
        if ( !$request->isMethod('post') ) {
            return response([
                'status' => -211,
                'message' => 'Method not allowed'
            ], 403);
        }
        $login = $validate = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);
        if( !Auth::attempt( $login ) ){
            return response(['status' => -211, 'message' => 'Invalid authentication data']);
        }
        $accessToken = Auth::user()->createToken('authToken')->accessToken;
        return response([
            'status' => 0,
            'user' => Auth::user(),
            'access_token' => $accessToken
        ]);
    }
}
