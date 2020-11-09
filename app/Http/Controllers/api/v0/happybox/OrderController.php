<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Payment;
use App\OrderCron;
use App\User;
use App\Userinfo;
use Validator;
use Auth;
use Config;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;
/** mail */
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrder;
use App\Mail\EboxDelivery;
use App\Mail\FullOrderSummary;
use App\Mail\OrderPaymentReceived;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function c_updt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'sendy_log' => 'required|string',
            'is_send' => 'required',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $pf = OrderCron::find($request->get('id'))->update([
            'sendy_log' => $request->get('sendy_log'),
            'is_send' => $request->get('is_send'),
        ]);
        return response([
            'status' => 0,
            'message' => 'Sendy log updated',
            'data' => $pf
        ], 200);

    }
    public function f_cron($date)
    {
        $order_date = date("Y-m-d ") . '23:59:59';
        $date = date('Y-m-d H:i:s', strtotime($order_date));
        $resp = OrderCron::where('is_send', false)
                ->where('created_at', '<=', $date)
                ->get();
        if(is_null($resp))
        {
            return response([
                'status' => 0,
                'message' => 'Freedom to play. No work to do',
            ], 200);
        }
        return response([
            'status' => 0,
            'data' => $resp->toArray(),
            'date' => $date
        ]);
    }
    public function new_cron(Request $request)
    {
        //'order_id', 'customer_buyer', 'box_voucher', 'order_meta', 'deliver_to', 'sendy_log', 'is_send',
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|string',
            'customer_buyer' => 'required|string',
            'box_voucher' => 'required|string',
            'order_meta' => 'required|string',
            'deliver_to' => 'required|string',
            'sendy_log' => 'required|string',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        if( OrderCron::where('order_id', $input['order_id'])->count() > 0 )
        {
            return response([
                'status' => 0,
                'message' => 'already created'
            ], 200);
        }
        OrderCron::create($input);
        return response([
            'status' => 0,
            'message' => 'created successfully'
        ], 200);
    }
    public function run_cron(Request $request)
    {
        //'order_id', 'customer_buyer', 'box_voucher', 'order_meta', 'deliver_to', 'sendy_log', 'is_send',
    }
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
    public function record_a_payment(Request $request)
    {
        try{
            $input = $request->all();
            Payment::create($input);
            $o = Order::where('order_id', $request->get('order'))->first();
            $o->paid = true;
            $o->payment_method = $request->get('method');
            $o->payment_status = 1;
            $o->payment_string = $request->get('pay_string');
            $o->order_evouchers = 'awaiting_allocation';
            $o->order_pvouchers = 'awaiting_allocation';
            $o->paid_amount = $request->get('amount');
            $o->save();
            $user = Auth::user()->email;
            Mail::to($user)
                ->send(new OrderPaymentReceived($o));
            $admin_user = Config::get('mail.from.address');
            Mail::to($admin_user)
                ->send(new NewOrder($o));
            return response([
                'status' => 0,
                'message' => 'created successfully'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error',
                'ref' => $input['ref']
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function mail_e_voucher(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'image' => 'required|string',
                'order_id' => 'required|string',
                'ebook' => 'required|string',
                'box' => 'required|string',
                'type' => 'required|string',
                'qty' => 'required',
                'price' => 'required',
                'cost' => 'required',
                'receiver_email' => 'required|string',
                'vouchers' => 'required|array'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $user = $input['receiver_email'];
            Mail::to($user)
                ->send( new EboxDelivery( $input ) );
            return response([
                'status' => 0,
                'message' => 'sent successfully'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error',
                'order' => $input['order_id']
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function mail_fullorder(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'buyer' => 'required|string',
                'order_id' => 'required|string',
                'mail_body' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $user = Auth::user()->email;
            Mail::to($user)
                ->cc([Config::get('mail.from.address')])
                ->send( new FullOrderSummary( json_decode(json_encode($input)) ) );
            return response([
                'status' => 0,
                'message' => 'sent successfully'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response([
                'status' => -211,
                'message' => 'Database server rule violation error',
                'order' => $input['order_id']
            ], 401);
        } catch (PDOException $e) {
            return response([
                'status' => -211,
                'message' => 'Database rule violation error'
            ], 401);
        }
    }
    public function findby_check_out_Req($check_req_id){
        try {
            $h =  Order::select('order_id','token')->where('paid', '!=', 1)->where('checkout_request_id', $check_req_id)->first();
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
    public function findby_ord_Req($order_id){
        try {
            $h =  Order::select('order_id','token')->where('paid', '!=', 1)->where('order_id', $order_id)->first();
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
    public function check_out_Req(Request $request, $order_id){
        $validator = Validator::make($request->all(), [
            'checkout_request_id' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $o = Order::where('order_id', $order_id)->first();
        $o->checkout_request_id = $request->get('checkout_request_id');
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
        $o = Order::where('order_id', $order_id)->first();
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
        $o = Order::where('order_id', $order_id)->first();
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
            $h =  Order::select('order_id','customer_buyer','subtotal','shipping_cost','order_totals','token')->where('order_id', $order_id)->first();
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
