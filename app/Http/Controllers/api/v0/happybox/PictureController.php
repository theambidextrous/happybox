<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Picture;
use Validator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $pics =  Picture::all();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $pics
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
    public function create(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'related_item' => 'required|string',
                // 'path_name' => 'required|string',
                'type' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty fields',
                    'errors' => $validator->errors()
                ], 401);
            }

            $input = $request->all();
            if(!$request->hasFile('path_name')){
                return response(['status' => -211, 'message'=> 'no valid file']);
            }
            $input['path_name'] = $this->upload_item($request);
            $pic = Picture::create($input);
            return response([
                'status' => 0,
                'message' => 'created successfully',
                'data' => null
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
            $pic =  Picture::find($id);
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $pic
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
    public function byitem($item, Request $request)
    {
        try {
            $pics =  Picture::where('related_item', $item)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $pics
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
        if(!$this->is_admin($request)){
            return response([
                'status' => -211,
                'message' => 'Permission denied'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'related_item' => 'required|string',
            'path_name' => 'required|string',
            'type' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty fields',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $pic = Picture::find($id);
        $pic->related_item = $request->get('related_item');
        $pic->path_name = $request->get('path_name');
        $pic->type = $request->get('type');
        if($pic->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'picture' => $id
            ]);
        }
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
    public function upload_item(Request $r){
        $file_name = strtolower($this->createCode(32)) . '.' . $r->file('path_name')->extension();
        $path = $r->file('path_name')->move(public_path('/media'), $file_name);
        return $file_url = url('/media/' . $file_name);
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