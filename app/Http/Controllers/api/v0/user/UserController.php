<?php

namespace App\Http\Controllers\api\v0\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Validator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            if ( !$request->isMethod('get') ) {
                return response([
                    'status' => -211,
                    'message' => 'Method not allowed'
                ], 403);
            }
            $users =  User::all();
            return response([
                'status' => 0,
                'message' => 'users list fetched successfully',
                'data' => $users
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function partners(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            if ( !$request->isMethod('get') ) {
                return response([
                    'status' => -211,
                    'message' => 'Method not allowed'
                ], 403);
            }
            $partners =  User::where('is_partner', true)->orderBy('id', 'asc')->take(1000000)->get();
            return response([
                'status' => 0,
                'message' => 'partners list fetched successfully',
                'data' => $partners
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function clients(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            if ( !$request->isMethod('get') ) {
                return response([
                    'status' => -211,
                    'message' => 'Method not allowed'
                ], 403);
            }
            $clients =  User::where('is_client', true)->orderBy('id', 'asc')->take(1000000)->get();
            return response([
                'status' => 0,
                'message' => 'clients list fetched successfully',
                'data' => $clients
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function admins(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            if ( !$request->isMethod('get') ) {
                return response([
                    'status' => -211,
                    'message' => 'Method not allowed'
                ], 403);
            }
            $admins =  User::where('is_admin', true)->orderBy('id', 'asc')->take(1000000)->get();
            return response([
                'status' => 0,
                'message' => 'admins list fetched successfully',
                'data' => $admins
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_client(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'c_password' => 'required|same:password'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $access_token = $user->createToken('authToken')->accessToken;
            $user['token'] = $access_token;
            // $success['user_id'] = $user->id();
            return response([
                'status' => 0,
                'message' => 'User created successfully',
                'data' => $user,
                'token' => $access_token
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }

    public function create_partner(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'c_password' => 'required|same:password'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['is_admin'] = false;
            $input['is_client'] = false;
            $input['is_partner'] = true;
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $access_token = $user->createToken('authToken')->accessToken;
            $user['token'] = $access_token;
            return response([
                'status' => 0,
                'message' => 'User created successfully',
                'data' => $user,
                'token' => $access_token
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }

    public function create_admin(Request $request)
    {
        try {
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'c_password' => 'required|same:password'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['is_admin'] = true;
            $input['is_client'] = false;
            $input['is_partner'] = false;
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $access_token = $user->createToken('authToken')->accessToken;
            $user['token'] = $access_token;
            return response([
                'status' => 0,
                'message' => 'User created successfully',
                'data' => $user,
                'token' => $access_token
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function is_admin($r){
        if( $r->user()->is_admin ){
            return true;
        }
        return false;
    }
    public function is_partner($r){
        if( $r->user()->is_partner ){
            return true;
        }
        return false;
    }
    public function is_client($r){
        if( $r->user()->is_client ){
            return true;
        }
        return false;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        try {
            if(!$this->is_admin($request) && $request->user()->id != $id){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $user =  User::where('id', $id)->first();
            return response([
                'status' => 0,
                'message' => 'user fetched successfully',
                'data' => $user
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function show_active($id){
        try {
            $u =  User::select(['is_active'])->where('id', $id)->first();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'is_active' => $u
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function showbyemail($email, Request $request){
        try {
            if(!$this->is_admin($request) && $request->user()->email != $email){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $user =  User::where('email', $email)->get();
            return response([
                'status' => 0,
                'message' => 'user fetched successfully',
                'data' => $user
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'email' => 'required|email'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $user = User::find($id);
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        if($user->save()){
            return response([
                'status' => 0,
                'message' => 'user updated successfully'
            ]);
        }
    }

    public function pwdreset(Request $request, $email)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'c_password' => 'required|same:password'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $user = User::where('email', $email)->first();
        $user->password = bcrypt($request->get('password'));
        if($user->save()){
            return response([
                'status' => 0,
                'message' => 'user password updated successfully'
            ]);
        }
    }
    public function request_pwdreset(Request $request, $email)
    {
        $user = User::where('email', $email)->first();
        if( !$user->id ){
            return response([
                'status' => -211,
                'message' => 'user not found'
            ]);
        }
        $reset_token = Str::uuid()->toString();
        /** email user a password reset token */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
