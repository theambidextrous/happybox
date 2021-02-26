<?php

namespace App\Http\Controllers\api\v0\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Userinfo;
use Validator;
use Config;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;
/**mailables */
use Illuminate\Support\Facades\Mail;
use App\Mail\PartnershipRequest;
use App\Mail\OnBoard;

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
                'message' => 'Invalid data. Make sure the email or phone is not already used'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Invalid data. Make sure the email or phone is not already used'
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
                'c_password' => 'required|same:password',
                'is_active' => 'string',
                'name' => 'string',
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
            $input['is_active'] = true;
            $user = $notify = User::create($input);
            $access_token = $user->createToken('authToken')->accessToken;
            $notify->sendEmailVerificationNotification();
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
                'message' => 'Invalid data. Make sure the email or phone is not already used'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Invalid data. Make sure the email or phone is not already used'
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
                'c_password' => 'required|same:password',
                'name' => 'string',
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
            $input['is_active'] = true;
            $input['password'] = bcrypt($input['password']);
            $user = $notify = User::create($input);
            $access_token = $user->createToken('authToken')->accessToken;
            $notify->sendEmailVerificationNotification();
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
                'message' => 'Invalid data. Make sure the email or phone is not already used'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Invalid data. Make sure the email or phone is not already used'
            ], 401);
        }
    }

    public function become_partner(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'fname' => 'required|string',
                'sname' => 'required|string',
                'short_description' => 'required|string',
                'location' => 'required|string',
                'phone' => 'required|string',
                'business_name' => 'required|string',
                'business_category' => 'required|string',
                'business_reg_no' => 'required|string'
                // 'services' => 'string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $payload = $request->all();
            $admin_user = Config::get('mail.from.admin');
            Mail::to($admin_user)->send(new PartnershipRequest($payload));
            Mail::to($payload['email'])->send(new OnBoard([
                'name' => $payload['fname'] . ' ' . $payload['sname']
            ]));
            return response([
                'status' => 0,
                'message' => 'Request sent successfully'
            ], 200);
        }catch (Exception $e) {
            return response([
                'status' => -211,
                'message' => $e->getMessage()
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
                'message' => 'Invalid data. Make sure the email or phone is not already used'
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Invalid data. Make sure the email or phone is not already used'
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
    public function adm_findall()
    {
        $data = [];
        $res = User::where('is_admin', true)->where('is_active', true)->get();
        if(!is_null($res))
        {
            $data = $res->toArray();
        }
        return response([
            'status' => 0,
            'message' => 'Admin user query returned',
            'data' => $this->format_adm_data($data),
        ], 200);
    }
    protected function format_adm_data($data)
    {
        $rtn = [];
        foreach( $data as $_data ):
            $admin_meta = Userinfo::where('userid', $_data['id'])->first();
            if(!is_null($admin_meta))
            {
                $_data['fname'] = $admin_meta->fname;
                $_data['sname'] = $admin_meta->sname;
                $_data['phone'] = $admin_meta->phone;
                $_data['internal_id'] = $admin_meta->internal_id;
            }
            array_push($rtn, $_data);
        endforeach;

        return $rtn;
    }
    public function new_admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string',
            'sname' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Input error. Make sure all fields are filled',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $extracted_username = explode('@', $input['email'])[0];
        $full_name = $input['fname'] . ' ' . $input['sname'];
        $user_data = [
            'username' => $extracted_username,
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => $input['password'],
            'name' => $full_name,
        ];
        $userid = $this->add_new_admin($user_data);
        $user_info_data = [
            'fname' => $input['fname'],
            'sname' => $input['sname'],
            'short_description' => 'admin user',
            'location' => 'no location captured',
            'phone' => $input['phone'],
            'userid' => $userid,
        ];
        $admin_internal_id = $this->add_info($user_info_data);
        return response([
            'status' => 0,
            'message' => 'admin user posted successfully',
            'data' => $admin_internal_id
        ], 200);
    }
    public function edit_admin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string',
            'sname' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Input error. Make sure all fields are filled',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $full_name = $input['fname'] . ' ' . $input['sname'];
        $user_data = [
            'email' => $input['email'],
            'phone' => $input['phone'],
            'name' => $full_name,
        ];
        if( strlen($input['password']) )
        {
            $user_data['password'] = bcrypt($input['password']);
        }
        User::find($id)->update($user_data);
        $user_info_data = [
            'fname' => $input['fname'],
            'sname' => $input['sname'],
            'phone' => $input['phone'],
        ];
        Userinfo::where('userid', $id)->update($user_info_data);
        return response([
            'status' => 0,
            'message' => 'admin user updated successfully',
            'data' => [],
        ], 200);
    }
    public function del_admin($id)
    {
        User::find($id)->delete();
        Userinfo::where('userid', $id)->delete();
        return response([
            'status' => 0,
            'message' => 'admin user deleted successfully',
            'data' => [],
        ], 200);
    }
    protected function add_new_admin($data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['is_active'] = true;
        $data['is_client'] = false;
        $data['is_partner'] = false;
        $data['is_admin'] = true;
        if( $this->mail_has_account($data['email']) )
        {
            $user = User::where('email', $data['email'])->first();
            if( !is_null($user) )
            {
                $user->name = $data['name'];
                $user->username = $data['username'];
                $user->password = $data['password'];
                $user->save();
                return $user->id;
            }
            else
            {
                $user_id = User::create($data)->id;
                return $user_id;
            }
        }
        $user_id = User::create($data)->id;
        return $user_id;
    }
    protected function add_info($data)
    {
        if( $this->id_has_info($data['userid']) )
        {
            $info = Userinfo::where('userid', $userid)->first();
            $info->fname = $data['fname'];
            $info->lname = $data['lname'];
            $info->phone = $data['phone'];
            $info->save();
            return $info->internal_id;
        }
        $data['internal_id'] = 'AD-' . $this->createCode(15);
        Userinfo::create($data);
        return $data['internal_id'];
    }
    protected function id_has_info($userid)
    {
        return Userinfo::where('userid', $userid)->count() > 0;
    }
    protected function mail_has_account($email)
    {
        return User::where('email', $email)->count() > 0;
    }
    public function change_pwd_admin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'c_password' => 'required|same:password'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Password error. Make sure passwords match',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        User::find($request->user()->id)->update($input);
        return response([
            'status' => 0,
            'message' => 'Password changed successfully',
            'data' => []
        ], 200);
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
    public function createCode($length = 20) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
