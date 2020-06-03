<?php

namespace App\Http\Controllers\api\v0;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Access\AuhtorizationException;
use App\User;
class VerificationController extends Controller
{

    protected $redirectTo = '';

    public function __construct(){
        $this->middleware('auth:api')->only('resend');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify','resend');
    }

    public function resend(Request $request)
    {
        if($request->user()->hasVerifiedEmail()){
            return response([
                'status' => 0,
                'message' => 'email already verified'
            ]);
        }
        $request->user()->sendEmailVerificationNotification();
        if( $request->wantsJson() ){
            return response([
                'status' => 0,
                'message' => 'Email verification link sent'
            ]);
        }
        // return redirect();
        return response([
            'status' => 0,
            'message' => 'Email verification link sent'
        ]);
        // return back()->with('resent', true);
    }

    public function verify(Request $request)
    {
        auth()->loginUsingId($request->route('id'));
        if( $request->route('id') != $request->user()->getKey() ){
            throw new AuthorizationException;
        }
        if($request->user()->hasVerifiedEmail()){
            return response([
                'status' => 0,
                'message' => 'email already verified'
            ]);
        }
        if($request->user()->markEmailAsVerified()){
            event( new Verified($request->user()) );
        }
        /** redirect to app client */
        return response([
            'status' => 0,
            'message' => 'email successfully verified'
        ]);
    }
}
?>