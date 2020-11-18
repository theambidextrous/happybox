<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Experience;
use Validator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $exp =  Experience::all();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $exp
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
                'partner' => 'required|string',
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|string',
                'topic' => 'required|string'
                ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $input['internal_id'] = 'EXP-' . $this->createCode(8);
            $exp = Experience::create($input);
            return response([
                'status' => 0,
                'message' => 'created successfully',
                'exp' => $input['internal_id']
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
            $exp =  Experience::find($id);
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $exp
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
    public function byidf($id, Request $request)
    {
        try {
            $exp =  Experience::where('internal_id', $id)->first();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $exp
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
    public function bytopic($id, Request $request)
    {
        try {
            $exp =  Experience::where('topic', $id)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $exp
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
    public function bypartner($id, Request $request)
    {
        try {
            $exp =  Experience::where('partner', $id)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $exp
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
            'partner' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|string',
            'topic' => 'required|string'
            ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $exp = Experience::find($id);
        $exp->partner = $request->get('partner');
        $exp->name = $request->get('name');
        $exp->description = $request->get('description');
        $exp->price = $request->get('price');
        $exp->topic = $request->get('topic');
        if($exp->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'exp' => $id
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
