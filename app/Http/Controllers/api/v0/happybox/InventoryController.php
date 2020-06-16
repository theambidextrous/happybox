<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use Validator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class InventoryController extends Controller
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
            $i =  Inventory::all();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
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

    public function get_report(Request $request)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'cols' => 'required|string',
                'date_from' => 'required|string',
                'date_to' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $col_arr = explode(',', $request->get('cols'));
            foreach ( $col_arr as $c_a ){
                if( strpos( $c_a, 'customer_buyer_id_' ) !== false ) {
                    $final_cols[] = 'customer_buyer_id';
                }elseif( strpos( $c_a, 'customer_user_id_' ) !== false ){
                    $final_cols[] = 'customer_user_id';
                }elseif( strpos( $c_a, 'box_internal_id_' ) !== false ){
                    $final_cols[] = 'box_internal_id';
                }else{
                    $final_cols[] = $c_a;
                }
            }
            $date_from = date('Y-m-d h:i:s', strtotime($request->get('date_from')));
            $date_to = date('Y-m-d h:i:s', strtotime($request->get('date_to')));
            $i =  Inventory::select($final_cols)->whereBetween('created_at', [$date_from, $date_to])->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error' . $e->getMessage()
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }

    public function by_voucher_status(Request $request, $status)
    {
        try{
            if(!$this->is_admin($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $i =  Inventory::where('box_voucher_status', $status)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
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

    public function by_voucher(Request $request, $v)
    {
        try{
            // if(!$this->is_admin($request)){
            //     return response([
            //         'status' => -211,
            //         'message' => 'Permission denied'
            //     ], 401);
            // }
            $i =  Inventory::where('box_voucher', $v)->first();
            if(empty($i)){
                $i =  Inventory::where('box_voucher_new', $v)->first(); 
            }
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
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
                'box_internal_id' => 'required|string',
                'box_type' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            if($input['box_type'] == '00'){
                $input['box_voucher'] = 'P-' . $this->createCode(8);
                $input['box_barcode'] = 'BX' . $this->createCode(16);
            }else{
                $input['box_voucher'] = 'E-' . $this->createCode(8); 
            }
            $h = Inventory::create($input);
            return response([
                'status' => 0,
                'message' => 'created successfully',
                'box' => $input['box_internal_id']
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
            $h =  Inventory::find($id);
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $h
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
    public function by_box($id, Request $request)
    {
        try {
            $h =  Inventory::where('box_internal_id', $id)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $h
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
    public function by_cust_user($cu, Request $request){
        try {
            $i =  Inventory::where('customer_user_id', $cu)->where('box_voucher_status', 6)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
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
    public function stock($box, Request $request){
        try {
            $i =  Inventory::where('box_internal_id', $box)->where('box_voucher_status', 1)->count();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'stock' => $i
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
    public function by_partner($ptn, Request $request)
    {
        try {
            $i =  Inventory::where('partner_internal_id', $ptn)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
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
    public function v_activate(Request $request, $voucher){
        $validator = Validator::make($request->all(), [
            'activation_date' => 'required|string',
            'customer_user_id' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $i =  Inventory::where('box_voucher', $voucher)->where('box_voucher_status', 2)->first();
        if(empty($i)){
            $i =  Inventory::where('box_voucher_new', $voucher)->where('box_voucher_status', 2)->first();
        }
        if(is_null($i)){
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' Is invalid',
                'voucher' => $voucher
            ]);
        }
        $i->box_voucher_status = 6;
        $i->customer_user_id = $request->get('customer_user_id');
        $i->voucher_activation_date = $request->get('activation_date');
        if($i->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'voucher' => $voucher
            ]);
        }
    }

    public function redeem_by_partner(Request $request, $voucher)
    {
        $validator = Validator::make($request->all(), [
            'redeemed_date' => 'required|string',
            'partner_identity' => 'required|string',
            'booking_date' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $i =  Inventory::where('box_voucher', $voucher)->first();
        if(empty($i)){
            $i =  Inventory::where('box_voucher_new', $voucher)->first();
        }
        $i->box_voucher_status = 3;
        $i->redeemed_date = $request->get('redeemed_date');
        $i->booking_date = $request->get('booking_date');
        $i->partner_internal_id = $request->get('partner_identity');
        if($i->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'voucher' => $voucher
            ]);
        }
    }
    public function update_c_buyer(Request $request)
    {
        
    }
    public function update_c_user(Request $request)
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
