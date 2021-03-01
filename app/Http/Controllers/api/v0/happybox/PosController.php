<?php

namespace App\Http\Controllers\api\v0\happybox;
// ini_set('max_execution_time', 300);
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Payment;
use App\OrderCron;
use App\User;
use App\Userinfo;
use App\Happybox;
use App\Picture;
use App\Inventory;
use App\Orderlog;
use App\Cancellation;

use Validator;
use Auth;
use Config;
use PDF;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;
/** mail */
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrder;
use App\Mail\EboxDelivery;
use App\Mail\FullOrderSummary;
use App\Mail\OrderPaymentReceived;

class PosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sale(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'fname' => 'required|string',
                'lname' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'quantity' => 'required|string',
                'boxname' => 'required|string|not_in:nn',
                'box_purchase_date' => 'required|string',
            ]);
            if (!intval($request->get('quantity'))) {
                return response([
                    'status' => -211,
                    'message' => 'Invalid quantity: ' . $request->get('quantity'),
                ], 401);
            }
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field detected',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            if( $this->find_stock($input['boxname']) < intval(abs($input['quantity'])) )
            {
                return response([
                    'status' => -211,
                    'message' => 'Insuficient stock error. There are no enough boxes to service this order.',
                ], 401);
            }
            $extracted_username = explode('@', $input['email'])[0];
            $full_name = $input['fname'] . ' ' . $input['lname'];
            $user_data = [
                'username' => $extracted_username,
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => $extracted_username,
                'name' => $full_name,
            ];
            $userid = $this->create_buyer($user_data);
            $user_info_data = [
                'fname' => $input['fname'],
                'sname' => $input['lname'],
                'short_description' => 'customer buyer via pos',
                'location' => 'no location captured',
                'phone' => $input['phone'],
                'userid' => $userid,
            ];
            $customer_buyer_id = $this->add_info($user_info_data);
            $voucher_assignment_data = [
                'customer_buyer_id' => $customer_buyer_id,
                'box_purchase_date' => $input['box_purchase_date'],
                'customer_buyer_invoice' => 'POS-' . $this->createCode(10),
                'box_internal_id' => $input['boxname'],
                'box_qty' => $input['quantity'],
            ];
            $this->assign_box($voucher_assignment_data);
            return response([
                'status' => 0,
                'message' => 'order successfully placed',
                'data' => []
            ], 200);
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
        }catch (Exception $e) {
            return response([
                'status' => -211,
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function findsales()
    {
        $data = Inventory::where('is_pos', true)
            ->where('box_voucher_status', 2)->orderBy('box_purchase_date', 'desc')->get();
        if( is_null($data) )
        {
            $data = [];
        }
        else
        {
            $data = $data->toArray();
        }
        return response([
            'status' => 0,
            'message' => 'pos sales found',
            'data' => $data,
        ], 200);
    }
    public function editsale(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'box_purchase_date' => 'required|string',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Form error. Select new purchase date',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $input['box_validity_date'] = date('Y-m-d',strtotime("+6 months",strtotime($input['box_purchase_date'])));
        $i = Inventory::find($id);
        if( !is_null($i) )
        {
            $i->box_purchase_date = $input['box_purchase_date'];
            $i->box_validity_date = $input['box_validity_date'];
            $i->save();
            return response([
                'status' => 0,
                'message' => 'sale purchase date modified',
                'data' => $i,
            ], 200);
        }
        return response([
            'status' => -211,
            'message' => 'Error. Entry not found',
            'data' => [],
        ], 401);
    }
    public function unsellsale($id)
    {
        $i = Inventory::find($id);
        if( intval($i->box_voucher_status) != 2)
        {
            return response([
                'status' => -211,
                'message' => 'Error. Box voucher could not be returned to stock.',
                'data' => [],
            ], 401);
        }
        if( !is_null($i) )
        {
            $i->order_number = null;
            $i->customer_buyer_id = null; 
            $i->customer_payment_method = null; 
            $i->box_purchase_date = null; 
            $i->box_validity_date = null; 
            $i->customer_buyer_invoice = null; 
            $i->box_voucher_status = 1; 
            $i->box_delivery_address = null; 
            $i->is_pos = false;
            $i->save();
            return response([
                'status' => 0,
                'message' => 'Box voucher has been returned to stock with "Instock" status.',
                'data' => [],
            ], 200);
        }
        return response([
            'status' => -211,
            'message' => 'Error. Entry not found',
            'data' => [],
        ], 401);
    }
    public function deletesale($id)
    {
        $i = Inventory::find($id)->delete();
        return response([
            'status' => 0,
            'message' => 'Entry was deleted',
            'data' => [],
        ], 200);
    }
    protected function find_stock($box) {
        return Inventory::where('box_internal_id', $box)
            ->where('box_voucher_status', 1)
            ->count();
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
    protected function assign_box($data) {
        $data['pay_ref'] = 'POS-' . $this->createCode(12);
        $data['customer_payment_method'] = 'POS offline';
        $data['order_number'] = $data['pay_ref'];
        $data['box_validity_date'] = date('Y-m-d',strtotime("+6 months",strtotime($data['box_purchase_date'])));
        $data['box_voucher_status'] = 2;
        $data['box_delivery_address'] = 'No physical address specified';
        $affected = Inventory::where('box_voucher_status', 1)/**in stock */
            ->where('box_type', '00')/** physical */
            ->where('box_internal_id', $data['box_internal_id'])/** current one */
            ->limit($data['box_qty'])
            ->update([
                'order_number' => $data['order_number'],
                'customer_buyer_id' => $data['customer_buyer_id'],
                'customer_payment_method' => $data['customer_payment_method'],
                'box_purchase_date' => $data['box_purchase_date'],
                'box_validity_date' => $data['box_validity_date'],
                'customer_buyer_invoice' => $data['customer_buyer_invoice'],
                'box_voucher_status' => $data['box_voucher_status'],
                'box_delivery_address' => $data['box_delivery_address'],
                'is_pos' => true,
            ]);
        if ($affected < 1) {

            Orderlog::create([
                'order_id' => $data['order_number'],
                'customer_buyer' => $data['customer_buyer_id'],
                'pay_ref' => $data['pay_ref'],
                'error_string' => 'No physical voucher has been assigned for the order ' . $data['order_number'] . ' the affected box is ' . $data['box_internal_id'],
            ]);
            throw new \Exception('No physical voucher has been assigned for the order ' . $data['order_number'] . ' the affected box is ' . $data['box_internal_id']);

        } elseif( $affected < $data['box_qty'] ) {
            $msg = 'Partial order physical voucher allocation. Only ' . $affected . ' of ' . $data['box_qty'] . ' vouchers where assigned.';
            Orderlog::create([
                'order_id' => $data['order_number'],
                'customer_buyer' => $data['customer_buyer_id'],
                'pay_ref' => $data['pay_ref'],
                'error_string' => $msg,
            ]);
            throw new \Exception($msg);
        }
        return true;
    }
    protected function create_buyer($data)
    {
        $data['password'] = bcrypt($data['password']);
        $data['is_active'] = true;
        $data['is_client'] = true;
        $data['is_partner'] = false;
        $data['is_admin'] = false;
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
            $info = Userinfo::where('userid', $data['userid'])->first();
            $info->fname = $data['fname'];
            $info->sname = $data['sname'];
            $info->phone = $data['phone'];
            $info->save();
            return $info->internal_id;
        }
        $data['internal_id'] = 'CU-' . $this->createCode(8);
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
    protected function phone_has_account($phone)
    {
        return User::where('phone', $phone)->count() > 0;
    }
}
