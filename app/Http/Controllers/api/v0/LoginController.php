<?php

namespace App\Http\Controllers\api\v0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

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
            return response(['status' => -211, 'message' => 'Invalid username or password']);
        }
        $accessToken = Auth::user()->createToken('authToken')->accessToken;
        $user = Auth::user();
        if(!$user['is_active']){
            return response(['status' => -211, 'message' => 'Account not active']);
        }
        if( $request->get('news') == '00'){
            $u = User::find($user['id']);
            $u->can_receive_news = 1;
            if($u->save()){
                return response([
                    'status' => 0,
                    'user' => $user,
                    'access_token' => $accessToken
                ]);
            }else{
                return response([
                    'status' => -211,
                    'message' => 'Could not subscribe your account to news updates'
                ]);
            }
        }else{
            return response([
                'status' => 0,
                'user' => $user,
                'access_token' => $accessToken
            ]);
        }

    }
    public function is_active(Request $request ){
        if(isset($request->user()->id)){
            return response(['status' => 0,'message' => 'is active']);
        }
        return response(['status' => -211,'message' => 'is inactive']);
    }
    // public function can_r_news(Request $request){
    //     if(isset($request->user()->id)){
    //         return response(['status' => 0,'message' => 'is active']);
    //     }
    //     return response(['status' => -211,'message' => 'is inactive']);
    // }
}
