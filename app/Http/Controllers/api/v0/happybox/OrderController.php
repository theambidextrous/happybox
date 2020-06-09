<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use Validator;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class OrderController extends Controller
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
            $o =  Order::all();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $o
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
            if(!$this->is_client($request)){
                return response([
                    'status' => -211,
                    'message' => 'Permission denied'
                ], 401);
            }
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|string',
                'customer_buyer' => 'required|string',
                'order_string' => 'required|string',
                'subtotal' => 'required|string',
                'shipping_cost' => 'required|string',
                'order_totals' => 'required|string',
                'token' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            Order::create($input);
            return response([
                'status' => 0,
                'message' => 'created successfully'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error',
                'order_id' => $input['order_id']
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
            $o =  Order::find($id);
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $o
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
    public function show_ex($order_id){
        try {
            $o =  Order::where('order_id', $order_id)->count();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'count' => $o
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
    public function by_order($order_id, Request $request)
    {
        try {
            $h =  Order::where('order_id', $order_id)->first();
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
    public function by_order_limited($order_id, Request $request)
    {
        try {
            $h =  Order::select('order_id','customer_buyer','subtotal','shipping_cost','order_totals')->where('order_id', $order_id)->first();
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
    public function by_customer($customer_buyer, Request $request)
    {
        try {
            $o =  Order::where('customer_buyer', $customer_buyer)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $o
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
    public function mark_paid_success(Request $request, $order_id){
        $validator = Validator::make($request->all(), [
            'order_evouchers' => 'required|string',
            'order_pvouchers' => 'required|string',
            'payment_method' => 'required|string',
            'payment_string' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $o = Order::find($order_id);
        $o->paid = true;
        $o->payment_method = $request->get('payment_method');
        $o->payment_status = 1;
        $o->payment_string = $request->get('payment_string');
        $o->order_evouchers = $request->get('order_evouchers');
        $o->order_pvouchers = $request->get('order_pvouchers');
        if($o->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'order' => $order_id
            ]);
        }
    }
    
    public function mark_paid_fail(Request $request, $order_id){
        //'order_id', 'customer_buyer', 'order_evouchers', 'order_pvouchers','order_string', 'subtotal', 'shipping_cost', 'order_totals', 'paid', 'payment_method', 'payment_status', 'payment_string','shipped', 'shipment_status', 'shipment_string',
        $validator = Validator::make($request->all(), [
            'order_evouchers' => 'required|string',
            'order_pvouchers' => 'required|string',
            'payment_method' => 'required|string',
            'payment_string' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $o = Order::find($order_id);
        $o->paid = false;
        $o->payment_method = $request->get('payment_method');
        $o->payment_status = 2;
        $o->payment_string = $request->get('payment_string');
        $o->order_evouchers = $request->get('order_evouchers');
        $o->order_pvouchers = $request->get('order_pvouchers');
        if($o->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'order' => $order_id
            ]);
        }
    }

    public function update_shipping(Request $request, $order_id){
        $validator = Validator::make($request->all(), [
            'shipment_string' => 'required|string',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $o = Order::find($order_id);
        $o->shipped = false;
        $o->shipment_status = 2;
        $o->shipment_string = $request->get('shipment_string');
        if($o->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'order' => $order_id
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        if(!$this->is_admin($request)){
            return response([
                'status' => -211,
                'message' => 'Permission denied'
            ], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|string',
            'description' => 'required|string',
            'topics' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $h = Order::find($id);
        $h->name = $request->get('name');
        $h->price = $request->get('price');
        $h->description = $request->get('description');
        $h->topics = $request->get('topics');
        if($h->save()){
            return response([
                'status' => 0,
                'message' => 'updated successfully',
                'box' => $id
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
