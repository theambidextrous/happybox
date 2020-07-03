<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use App\Orderlog;
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
    public function assign_c_buyer_pbox(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'box_internal_id' => 'required|string',
                'order_number' => 'required|string',
                'customer_buyer_id' => 'required|string',
                'customer_payment_method' => 'required|string',
                'box_purchase_date' => 'required|string',
                'box_validity_date' => 'required|string',
                'customer_buyer_invoice' => 'required|string',
                'box_qty' => 'required|string',
                'box_voucher_status' => 'required|string',
                'box_delivery_address' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $pay_method_Ref = explode('~', $request->get('customer_payment_method'));
            $input['pay_ref'] = $pay_method_Ref[0];
            $input['customer_payment_method'] = $pay_method_Ref[1];
            $affected = Inventory::where('box_voucher_status', '1')/**in stock */
                        ->where('box_type', '00')/** physical */
                        ->where('box_internal_id', $request->get('box_internal_id')) /** current one */
                        ->limit($request->get('box_qty'))
                        ->update([
                            'order_number' => $request->get('order_number'),
                            'customer_buyer_id' => $request->get('customer_buyer_id'),
                            'customer_payment_method' => $input['customer_payment_method'],
                            'box_purchase_date' => $request->get('box_purchase_date'),
                            'box_validity_date' => $request->get('box_validity_date'),
                            'customer_buyer_invoice' => $request->get('customer_buyer_invoice'),
                            'box_voucher_status' => $request->get('box_voucher_status'),
                            'box_delivery_address' => $request->get('box_delivery_address')
                        ]);
            if( $affected < 1 ){
                $msg = 'No physical voucher has been assigned for the order ' . $request->get('order_number') . ' the affected box is ' . $input['box_internal_id'];
                Orderlog::create([
                    'order_id' => $request->get('order_number'),
                    'customer_buyer' => $request->get('customer_buyer_id'),
                    'pay_ref' => $input['pay_ref'],
                    'error_string' => $msg 
                ]);
                return response([
                    'status' => -211,
                    'message' => $msg,
                    'box' => $input['box_internal_id']
                ], 401);
            }
            elseif( $affected < $request->get('box_qty') ){
                $msg = 'Partial order physical voucher allocation. Only '.$affected.' of '.$request->get('box_qty').' vouchers where assigned.';
                Orderlog::create([
                    'order_id' => $request->get('order_number'),
                    'customer_buyer' => $request->get('customer_buyer_id'),
                    'pay_ref' => $input['pay_ref'],
                    'error_string' => $msg 
                ]);
                return response([
                    'status' => 0,
                    'update_status' => 1,
                    'message' => $msg,
                    'box' => $input['box_internal_id']
                ], 200);
            }
            return response([
                'status' => 0,
                'message' => 'created successfully',
                'box' => $input['box_internal_id']
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            Orderlog::create([
                'order_id' => $request->get('order_number'),
                'customer_buyer' => $request->get('customer_buyer_id'),
                'pay_ref' => $request->get('customer_payment_method'),
                'error_string' => $e->getMessage() 
            ]);
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            Orderlog::create([
                'order_id' => $request->get('order_number'),
                'customer_buyer' => $request->get('customer_buyer_id'),
                'pay_ref' => $request->get('customer_payment_method'),
                'error_string' => $e->getMessage() 
            ]);
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function create_c_buyer_ebox(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'box_internal_id' => 'required|string',
                'order_number' => 'required|string',
                'customer_buyer_id' => 'required|string',
                'customer_payment_method' => 'required|string',
                'box_purchase_date' => 'required|string',
                'box_validity_date' => 'required|string',
                'customer_buyer_invoice' => 'required|string',
                'box_vouchers' => 'required|string',
                'box_voucher_status' => 'required|string',
                'box_delivery_address' => 'required|string'
            ]);
            if( $validator->fails() ){
                Orderlog::create([
                    'order_id' => $request->get('order_number'),
                    'customer_buyer' => $request->get('customer_buyer_id'),
                    'pay_ref' => $request->get('customer_payment_method'),
                    'error_string' => $validator->errors()->toJson() 
                ]);
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $evouchers_arr = explode(',', $request->get('box_vouchers'));
            $input = $request->all();
            $input['box_type'] = '11';
            $input['order_number'] = $input['order_number'];
            foreach( $evouchers_arr as $evoucher ):
                $input['box_voucher'] = $evoucher;
                $h = Inventory::create($input);
            endforeach;
            return response([
                'status' => 0,
                'message' => 'created successfully',
                'box' => $input['box_internal_id']
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            Orderlog::create([
                'order_id' => $request->get('order_number'),
                'customer_buyer' => $request->get('customer_buyer_id'),
                'pay_ref' => $request->get('customer_payment_method'),
                'error_string' => $e->getMessage() 
            ]);
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            Orderlog::create([
                'order_id' => $request->get('order_number'),
                'customer_buyer' => $request->get('customer_buyer_id'),
                'pay_ref' => $request->get('customer_payment_method'),
                'error_string' => $e->getMessage()
            ]);
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }

    public function find_o_voucher(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string',
                'customer_buyer_id' => 'required|string',
                'box_internal_id' => 'required|string',
                'receiver' => 'required|string',
                'type' => 'required|string'
            ]);
            if( $validator->fails() ){
                Orderlog::create([
                    'order_id' => $request->get('order_id'),
                    'customer_buyer' => $request->get('customer_buyer_id'),
                    'pay_ref' => $request->get('box_internal_id'),
                    'error_string' => $validator->errors()->toJson() 
                ]);
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $found_vouchers = Inventory::select(['box_voucher'])
                            ->where('order_number', $input['order_id'])
                            ->where('customer_buyer_id', $input['customer_buyer_id'])
                            ->where('box_internal_id', $input['box_internal_id'])
                            ->where('box_delivery_address', $input['receiver'])
                            ->where('box_type', $input['type'])
                            ->where('box_voucher_status', '2')->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'vouchers' => $found_vouchers->toArray()
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            Orderlog::create([
                'order_id' => $request->get('order_id'),
                'customer_buyer' => $request->get('customer_buyer_id'),
                'pay_ref' => $request->get('box_internal_id'),
                'error_string' => $e->getMessage() 
            ]);
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error'
            ], 401);
        } catch (PDOException $e) {
            Orderlog::create([
                'order_id' => $request->get('order_id'),
                'customer_buyer' => $request->get('customer_buyer_id'),
                'pay_ref' => $request->get('box_internal_id'),
                'error_string' => $e->getMessage() 
            ]);
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
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
