<?php

namespace App\Http\Controllers\api\v0\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Userinfo;
use App\User;
use Validator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class UserinfoController extends Controller
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
            $usersinfo =  Userinfo::all();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $usersinfo
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
    public function create_for_partner($id, Request $request)
    {
        try{
            if(!$this->is_admin($request) && $request->user()->id != $id){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string',
                'sname' => 'required|string',
                'short_description' => 'required|string',
                'location' => 'required|string',
                'phone' => 'required|string',
                'business_name' => 'required|string',
                'business_category' => 'required|string',
                'business_reg_no' => 'required|string',
                'services' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['internal_id'] = 'PT-' . $this->createCode(10);
            $input['userid'] = $id;
            $user = Userinfo::create($input);
            return response([
                'status' => 0,
                'message' => 'Account updated successfully',
                'userid' => $id,
                'internal_id' => $input['internal_id']
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

    public function create_for_client($id, Request $request)
    {
        try{
            if(!$this->is_admin($request) && $request->user()->id != $id){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string',
                'sname' => 'required|string',
                'short_description' => 'string',
                'location' => 'string',
                'phone' => 'string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['internal_id'] = 'CU-' . $this->createCode(8);
            $input['userid'] = $id;
            $user = Userinfo::create($input);
            return response([
                'status' => 0,
                'message' => 'Account updated successfully',
                'userid' => $id,
                'internal_id' => $input['internal_id']
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

    public function create_for_admin($id, Request $request)
    {
        try{
            if(!$this->is_admin($request) && $request->user()->id != $id){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string',
                'sname' => 'required|string',
                'short_description' => 'string',
                'location' => 'string',
                'phone' => 'string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['internal_id'] = 'AD-' . $this->createCode(15);
            $input['userid'] = $id;
            $user = Userinfo::create($input);
            return response([
                'status' => 0,
                'message' => 'Account updated successfully',
                'userid' => $id
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

    /** utitlties */
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
    public function change_profile(Request $request, $id){
        try{
            if(!$this->is_admin($request) && $request->user()->id != $id){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $user = Userinfo::where('userid', $id)->first();
            $prof_path = $this->upload_item($request);
            $user->picture = $prof_path;
            if($user->save()){
                return response([
                    'status' => 0,
                    'message' => 'profile photo updated successfully',
                    'userid' => $id,
                    'path' => $prof_path
                ], 200);
            }
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
    public function upload_item(Request $r){
        $f = strtolower($this->createCode(32)) . '.' . $r->file('img')->extension();
        $path = $r->file('img')->move(public_path('/media/profiles'), $f);
        return $file_url = url('/media/profiles/' . $f);
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
            $userinfo =  Userinfo::where('userid', $id)->first();
            return response([
                'status' => 0,
                'message' => 'user fetched successfully',
                'data' => $userinfo
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

    public function show_byidf($id, Request $request)
    {
        try {
            // if(!$this->is_admin($request) && $request->user()->id != $id){
            //     return response([
            //         'status' => -211,
            //         'message' => 'Permission denied'
            //     ], 401);
            // }
            $userinfo =  Userinfo::where('internal_id', $id)->first();
            return response([
                'status' => 0,
                'message' => 'user fetched successfully',
                'data' => $userinfo
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
    
    public function show_bytopic($topic, Request $request){
        try {
            $u =  Userinfo::select('userid','internal_id','short_description','location','picture','business_name')->where('internal_id', 'like', '%PT-%')->where('business_category', $topic)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $u
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
    public function show_ptn_all(){
        try {
            $u =  Userinfo::select('userid','internal_id','picture','business_name')->where('internal_id', 'like', '%PT-%')->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $u
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
    public function update_partner(Request $request, $id)
    {
        if(!$this->is_admin($request) && $request->user()->id != $id){
            return response([
                'status' => -211,
                'message' => 'Permission denied'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string',
            'sname' => 'required|string',
            'short_description' => 'required|string',
            'location' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string',
            'business_name' => 'required|string',
            'business_category' => 'required|string',
            'business_reg_no' => 'required|string',
            'services' => 'string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $input['internal_id'] = 'PT-' . $this->createCode(8);
        $input['userid'] = $id;
        $userinfo = Userinfo::where('userid', $id)->first();
        User::find($id)->update(['email' => $request->get('email')]);
        if(!$userinfo){
            Userinfo::create($input);
            return response([
                'status' => 0,
                'message' => 'user updated successfully',
                'userid' => $id,
                'internal_id' => $input['internal_id']
            ]);
        }
        $internal_id = $userinfo->internal_id;
        $userinfo->fname = $request->get('fname');
        $userinfo->sname = $request->get('sname');
        $userinfo->short_description = $request->get('short_description');
        $userinfo->location = $request->get('location');
        $userinfo->phone = $request->get('phone');
        $userinfo->business_name = $request->get('business_name');
        $userinfo->business_category = $request->get('business_category');
        $userinfo->business_reg_no = $request->get('business_reg_no');
        if($request->get('services')){
            $userinfo->services = $request->get('services');
        }
        if($userinfo->save()){
            return response([
                'status' => 0,
                'message' => 'user updated successfully',
                'userid' => $id,
                'internal_id' => $internal_id
            ]);
        }
    }
    public function update_client(Request $request, $id)
    {
        if(!$this->is_admin($request) && $request->user()->id != $id){
            return response([
                'status' => -211,
                'message' => 'Permission denied'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string',
            'sname' => 'required|string',
            'short_description' => 'string',
            'location' => 'string',
            'phone' => 'string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $userinfo = Userinfo::where('userid', $id)->first();
        if(!$userinfo){
            $input['userid'] = $id;
            $input['internal_id'] = 'CU-' . $this->createCode(8);
            Userinfo::create($input);
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $userinfo->fname = $request->get('fname');
        $userinfo->sname = $request->get('sname');
        $userinfo->short_description = $request->get('short_description');
        $userinfo->location = $request->get('location');
        $userinfo->phone = $request->get('phone');
        if($userinfo->save()){
            return response([
                'status' => 0,
                'message' => 'user updated successfully',
                'userid' => $id
            ]);
        }
    }
    public function update_admin(Request $request, $id)
    {
        if(!$this->is_admin($request) && $request->user()->id != $id){
            return response([
                'status' => -211,
                'message' => 'Permission denied'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string',
            'sname' => 'required|string',
            'short_description' => 'string',
            'location' => 'string',
            'phone' => 'string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $userinfo = Userinfo::where('userid', $id)->first();
        $userinfo->fname = $request->get('fname');
        $userinfo->sname = $request->get('sname');
        $userinfo->short_description = $request->get('short_description');
        $userinfo->location = $request->get('location');
        $userinfo->phone = $request->get('phone');
        if($userinfo->save()){
            return response([
                'status' => 0,
                'message' => 'user updated successfully',
                'userid' => $id
            ]);
        }
    }
    
    /** image upload */
    public function upload_img(Request $request, $id)
    {
        
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
