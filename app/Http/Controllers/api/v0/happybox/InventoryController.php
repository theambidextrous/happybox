<?php
namespace App\Http\Controllers\api\v0\happybox;
// ini_set('max_execution_time', 300);
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;
use App\Userinfo;
use App\User;
use App\Orderlog;
use App\Cancellation;
use Validator;
use Auth;
use Config;
use DNS1D;
use DNS2D;
use PDF;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;
/**mailables */
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationFailed;
use App\Mail\ActivationFailedAdmin;
use App\Mail\CancellationFailed;
use App\Mail\CancellationSucess;
use App\Mail\ActivationSuccess;
use App\Mail\ModificationSuccess;
use App\Mail\PartnerCancellationAdmin;
use App\Mail\PartnerCancellation;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function bcode($barcodenumber, $type = 'C39'){
        $img_src = "data:image/png;base64,";
        if( in_array($type, ['QRCODE','PDF417','DATAMATRIX']) ){
            $barcode = DNS2D::getBarcodePNG($barcodenumber, $type, 2,60, array(1, 133, 182));
            return response([
                'status' => 0,
                'message' => 'success',
                'source' => $img_src.$barcode
            ], 200); 
        }else{
            $barcode = DNS1D::getBarcodePNG($barcodenumber, $type, 2,60, array(1, 133, 182));
            return response([
                'status' => 0,
                'message' => 'success',
                'source' => $img_src.$barcode
            ], 200); 
        }
    }
    public function bcodev(Request $request){
        $validator = Validator::make($request->all(), [
            'box_internal_id' => 'required|string',
            'stock_type' => 'required|integer',
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $input = $request->all();
        $barcodev = Inventory::select(['box_barcode','box_voucher','box_voucher_status'])
            ->where('box_voucher_status', 1)
            ->where('box_internal_id', $input['box_internal_id'])
            ->where('box_type', '00')
            ->get();
        if($input['stock_type'] == 0 ){
            $barcodev = Inventory::select(['box_barcode','box_voucher','box_voucher_status'])
                ->where('box_internal_id', $input['box_internal_id'])
                ->where('box_type', '00')
                ->get();
        }
        return response([
            'status' => 0,
            'message' => 'success',
            'data' => $barcodev
        ], 200);
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
            $i =  Inventory::where('box_voucher', $v)->first();
            if(is_null($i)){
                return response([
                    'status' => -211,
                    'message' => 'The voucher is invalid',
                    'data' => null
                ]);
            }
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $i
            ]);
            // if($i->box_voucher_status == 3){
            //     return response([
            //         'status' => -211,
            //         'message' => 'The voucher is already redeemed',
            //         'data' => null
            //     ]);
            // }
            // $voucher_valid_date = date('Y-m-d', strtotime($i->box_validity_date));
            // $today_date = date('Y-m-d', strtotime('now'));
            // if( $today_date > $voucher_valid_date ){
            //     return response([
            //         'status' => -211,
            //         'message' => 'The voucher is expired!',
            //         'data' => null
            //     ]);
            // }
            // if($i->box_voucher_status == 6){
            //     return response([
            //         'status' => 0,
            //         'message' => 'fetched successfully',
            //         'data' => $i
            //     ]);
            // }
            // return response([
            //     'status' => -211,
            //     'message' => 'Unrecognized voucher code. ',
            //     'data' => null
            // ]);
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
                $input['box_barcode'] = 'BX' . $this->createCode(8);
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
            $i =  Inventory::where('customer_user_id', $cu)
                ->where('box_voucher_status', '!=', 1)
                ->where('box_voucher_status', '!=', 2)
                ->where('box_voucher_status', '!=', 7)
                ->get();
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
            $i =  Inventory::where('partner_internal_id', $ptn)
                    ->where('box_voucher_status', '!=', 1)
                    ->where('box_voucher_status', '!=', 2)
                    ->where('box_voucher_status', '!=', 6)
                    ->where('box_voucher_status', '!=', 7)
                    ->get();
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
    public function v_cancel(Request $request, $voucher)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_date' => 'required|string',
            'customer_user_id' => 'required|string',
            'cancelled_by' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        if(substr( $voucher, 0, 2 ) === "R-"){ /** replacement voucher cant cancel */
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' has already been replaced. A voucher can only be replaced once.',
                'voucher' => $voucher
            ]);
        }
        $i =  Inventory::where('box_voucher', $voucher)->first();
        $new_inventory = $i;
        if(is_null($i)){
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' Is Unknown',
                'voucher' => $voucher
            ]);
        }
        if($i->box_voucher_status == 6){ /** is activated --check validity */
            $voucher_valid_date = date('Y-m-d', strtotime($i->box_validity_date));
            $today_date = date('Y-m-d', strtotime('now'));
            if( $today_date > $voucher_valid_date ){/** voucher is expired */
                return response([
                    'status' => -211,
                    'message' => 'Voucher '.$voucher.' is expired',
                    'voucher' => $voucher
                ]);
            }else{/** replace */
                $new_box_voucher = 'R-' . $this->createCode(8);
                $new_inventory->box_voucher = $new_box_voucher;
                $new_inventory->box_voucher_status = 2;/**back to purchased */
                $new_inventory->voucher_activation_date = null;
                $new_inventory->redeemed_date = null; 
                $new_inventory->cancellation_date = null; 
                $new_inventory->booking_date = null; 
                $new_inventory->partner_pay_due_date = null; 
                $new_inventory->partner_pay_effec_date = null; 
                $new_inventory->partner_pay_amount = null; 
                $new_inventory->partner_internal_id = null; 
                $new_inventory->partner_invoice = null; 
                $new_inventory->redeemed_service = null;
                $input = $new_inventory->toArray();
                unset($input['id']);/** remove unq key */
                /** save old one as was b4 */
                $i->box_voucher_new = $new_box_voucher;
                $i->box_voucher = $voucher;
                $i->cancellation_date = $request->get('cancellation_date');
                $i->box_voucher_status = 4;/** set cancel it */
                $pdf_data = [
                    'box_voucher' => $new_box_voucher,
                ];
                $voucher_attachement = null;
                try {
                    $voucher_attachement = $this->vourcher_attach([$pdf_data]);
                } catch ( Exception $e ) {
                    return response([
                        'status' => -211,
                        'message' => $e->getMessage(),
                    ]);
                }
                if($i->save() && Inventory::create($input)){
                    $cancellation_payload = [
                        'cancelled_voucher' => $voucher,
                        'new_voucher' => $new_box_voucher,
                        'reason' => 'Declared lost by user ' . Auth::user()->email,
                        'partner' => 'N/A. Cancelled by user'
                    ];
                    Cancellation::create($cancellation_payload);
                    $payload = [
                        'message' => 'Voucher '.$voucher.' has been cancelled and replaced by a new voucher code ' .$new_box_voucher,
                        'voucher' => $new_box_voucher,
                        'c_buyer' => Auth::user()->name,
                        'evoucher_attachment' => $voucher_attachement,
                    ];
                    $user = Auth::user()->email;
                    Mail::to($user)->send(new CancellationSucess($payload));
                    return response([
                        'status' => 0,
                        'message' => 'Voucher '.$voucher.' has been cancelled and replaced by a new voucher ' .$new_box_voucher,
                        'voucher' => $new_box_voucher
                    ]);
                }
                return response([
                    'status' => -211,
                    'message' => 'Action failed',
                ]);
            }
        }else{/** code is after redemption */
            $payload = [
                'message' => 'Voucher '.$voucher.' which you tried to replace is no longer valid for replacement. Most likely it has been redeemed already',
                'voucher' => $voucher
            ];
            $user = Auth::user()->email;
            Mail::to($user)->send(new CancellationFailed($payload));
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' has no selling date attached',
                'voucher' => $voucher
            ]);
        }
    }
    protected function vourcher_attach($data)
    {
        $file_name = (string) Str::uuid() . '.pdf';
        //, [], $this->pdf_config()
        PDF::loadView('emails.orders.evoucher_attach', [ 'data' => $data ])->save(public_path('hh4c16wwv73khin1oh2vasty8lqzuei0/' . $file_name));
        return $file_name;
    }
    protected function pdf_config()
    {
        return [
            'format' => 'A4',
            'margin_top' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'P',
            'display_mode' => 'fullpage',
        ];
    }
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
        $i =  Inventory::where('box_voucher', $voucher)->first();
        if(is_null($i)){
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' Is Unknown',
                'voucher' => $voucher
            ]);
        }
        if( $i->box_voucher_status == 3){/** already redeemed */
            $payload = [
                'message' => 'Voucher '.$voucher.' Which you tried to activate is already redeemed',
                'voucher' => $voucher
            ];
            $user = Auth::user()->email;
            Mail::to($user)
                ->send(new ActivationFailed($payload));
            $admin_user = Config::get('mail.from.address');
            Mail::to($admin_user)
                ->send(new ActivationFailedAdmin($payload));
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' Is already redeemed',
                'voucher' => $voucher
            ]);
        }
        if( $i->box_voucher_status == 6){/** already activated */
            $payload = [
                'message' => 'Voucher '.$voucher.' Which you tried to activate is already activated',
                'voucher' => $voucher
            ];
            $user = Auth::user()->email;
            Mail::to($user)
                ->send(new ActivationFailed($payload));
            $admin_user = Config::get('mail.from.address');
            Mail::to($admin_user)
                ->send(new ActivationFailedAdmin($payload));
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' Is already activated',
                'voucher' => $voucher
            ]);
        }
        if( $i->box_voucher_status == 1){/** no selling date */
            $payload = [
                'message' => 'Voucher '.$voucher.' Which you tried to activate has no selling date',
                'voucher' => $voucher
            ];
            $user = Auth::user()->email;
            Mail::to($user)
                ->send(new ActivationFailed($payload));
            $admin_user = Config::get('mail.from.address');
            Mail::to($admin_user)
                ->send(new ActivationFailedAdmin($payload));
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' has no selling date attached',
                'voucher' => $voucher
            ]);
        }
        $voucher_valid_date = date('Y-m-d', strtotime($i->box_validity_date));
        $today_date = date('Y-m-d', strtotime('now'));
        if( $today_date > $voucher_valid_date || $i->box_voucher_status == 5){/**voucher expired */
            $i->box_voucher_status = 5; /** mark as expired */
            $i->save();
            $payload = [
                'message' => 'Voucher '.$voucher.' Which you tried to activate is no longer valid',
                'voucher' => $voucher
            ];
            $user = Auth::user()->email;
            Mail::to($user)
                ->send(new ActivationFailed($payload));
            $admin_user = Config::get('mail.from.address');
            Mail::to($admin_user)
                ->send(new ActivationFailedAdmin($payload));
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' has no selling date attached',
                'voucher' => $voucher
            ]);
        }
        if( $i->box_voucher_status == 2){ /** purchased-- can be activated */
            $i->box_voucher_status = 6;
            $i->customer_user_id = $request->get('customer_user_id');
            $i->voucher_activation_date = $request->get('activation_date');
            if($i->save()){
                $payload = [
                    'message' => 'Voucher '.$voucher.' has been successfully activated. Go ahead and redeem.',
                    'voucher' => $voucher
                ];
                $user_email = Auth::user()->email;
                Mail::to($user_email)->send(new ActivationSuccess(['name' => Auth::user()->name ]));
                return response([
                    'status' => 0,
                    'message' => 'Voucher activated successfully. You can now redeem it!',
                    'voucher' => $voucher,
                    'validity' => $voucher_valid_date
                ]);
            }
        }else{
            return response([
                'status' => -211,
                'message' => 'Unknown voucher!',
                'voucher' => $voucher
            ]);
        }
    }
    public function ptn_pay_effec_dt(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'partner_pay_effec_date' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $i =  Inventory::find($id);
        if(is_null($i)){
            return response([
                'status' => -211,
                'message' => 'Entry not found'
            ]);
        }
        $i->partner_pay_effec_date = $request->get('partner_pay_effec_date');
        if($i->save()){
            return response([
                'status' => 0,
                'message' => 'Date posted!'
            ]);
        }
        return response([
            'status' => -211,
            'message' => 'mission failed again'
        ]);

    }
    public function redeem_by_partner(Request $request, $voucher)
    {
        $validator = Validator::make($request->all(), [
            'redeemed_date' => 'required|string',
            'partner_identity' => 'required|string',
            'booking_date' => 'required|string',
            'redeemed_service' => 'required|string',
            'partner_pay_amount' => 'required'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $i =  Inventory::where('box_voucher', $voucher)->first();
        if( $i->box_voucher_status == 6 ){ /** is in activated state */
            $i->box_voucher_status = 3;
            $i->redeemed_date = $request->get('redeemed_date');
            $i->booking_date = $request->get('booking_date');
            $i->partner_internal_id = $request->get('partner_identity');
            $i->partner_pay_amount = $request->get('partner_pay_amount');
            // $i->partner_pay_effec_date = $request->get('redeemed_date');//day they get paid
            $i->partner_pay_due_date = $this->nxt_month_date($request->get('booking_date'));
            $i->partner_invoice = 'INV-' . $this->createCode(6);
            $i->redeemed_service = $request->get('redeemed_service');
            if($i->save()){
                return response([
                    'status' => 0,
                    'message' => 'Voucher '.$voucher.' has been successfully redeemed. The customer has been booked for '.$request->get('redeemed_service').' on '.$request->get('booking_date'),
                    'voucher' => $voucher
                ]);
            }
        }
        return response([
            'status' => -211,
            'message' => 'Voucher '.$voucher.' could not be redeemed.',
            'voucher' => $voucher
        ]);
    }
    protected function nxt_month_date($bdate)
    {
        $month =  Date("Y-m", strtotime($bdate . " +1 month"));
        $date_ = date('Y-m-d', strtotime($month . '-15'));
        return $date_;
    }
    public function modify_booking(Request $request, $voucher)
    {
        $validator = Validator::make($request->all(), [
            'new_booking_date' => 'required|string',
            'partner_identity' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $i =  Inventory::where('box_voucher', $voucher)
                ->where('partner_internal_id', $request->get('partner_identity'))
                ->first();
        if( $i->box_voucher_status == 3 ){ /** is in redeemed state */
            $i->booking_date = $request->get('new_booking_date');
            $i->partner_pay_due_date = $this->nxt_month_date($request->get('new_booking_date'));
            if($i->save()){
                $ptn = Userinfo::where('internal_id', $request->get('partner_identity'))->first();
                $usr = Userinfo::where('internal_id', $i->customer_user_id)->first();
                $this_usr = User::find($usr->userid);
                $payload = [
                    'message' => 'Your booking date for the voucher '.$voucher.' 
                    has been changed to '.date('d/m/Y', strtotime($request->get('new_booking_date'))).' by ' . $ptn->business_name,
                    'date' => date('d/m/Y', strtotime($request->get('new_booking_date')))
                ];
                $user_email = $this_usr->email;
                Mail::to($user_email)->send(new ModificationSuccess($payload));
                return response([
                    'status' => 0,
                    'message' => 'The booking date has been successfully changed. The customer has been notified of these changes',
                    'voucher' => $voucher
                ]);
            }
        }
        return response([
            'status' => -211,
            'message' => 'General error occured.',
            'voucher' => $voucher
        ]);
    }
    public function cancel_booking(Request $request, $voucher)
    {
        if(substr( $voucher, 0, 2 ) === "R-"){ /** replacement voucher cant cancel */
            return response([
                'status' => -211,
                'message' => 'Voucher '.$voucher.' has already been replaced. A voucher can only be replaced once.',
                'voucher' => $voucher
            ]);
        }
        $validator = Validator::make($request->all(), [
            'cancellation_date' => 'required|string',
            'partner_identity' => 'required|string',
            'reason' => 'required|string'
        ]);
        if( $validator->fails() ){
            return response([
                'status' => -211,
                'message' => 'Invalid or empty field',
                'errors' => $validator->errors()
            ], 401);
        }
        $i =  Inventory::where('box_voucher', $voucher)
                ->where('partner_internal_id', $request->get('partner_identity'))
                ->first();
        $new_inventory = $i;
        if( $i->box_voucher_status == 3 ){ /** is in redeemed state */
            $voucher_valid_date = date('Y-m-d', strtotime($i->box_validity_date));
            $today_date = date('Y-m-d', strtotime('now'));
            if( $today_date > $voucher_valid_date ){/** voucher is expired */
                return response([
                    'status' => -211,
                    'message' => 'Voucher '.$voucher.' is expired',
                    'voucher' => $voucher
                ]);
            }else{
                $new_box_voucher = 'R-' . $this->createCode(8);
                /** save old */
                $input_data = [
                    'partner_internal_id' => $request->get('partner_identity'),
                    'box_voucher_new' => $new_box_voucher,
                    'box_voucher' => $voucher,
                    'cancellation_date' => $request->get('cancellation_date'),
                    'box_voucher_status' => 4/** set cancel it */
                ];
                $input_new = $i->toArray();
                Inventory::find($input_new['id'])->update($input_data);

                $new_inventory->box_voucher = $new_box_voucher;
                $new_inventory->box_voucher_status = 2;/**back to purchased */
                $new_inventory->redeemed_date = null; 
                $new_inventory->voucher_activation_date = null;
                $new_inventory->cancellation_date = null; 
                $new_inventory->booking_date = null; 
                $new_inventory->partner_pay_due_date = null; 
                $new_inventory->partner_pay_effec_date = null; 
                $new_inventory->partner_pay_amount = null; 
                $new_inventory->partner_internal_id = null; 
                $new_inventory->partner_invoice = null; 
                $new_inventory->redeemed_service = null;
                $input = $new_inventory->toArray();
                unset($input['id']);/** remove unq key */
                $pdf_data = [
                    'box_voucher' => $new_box_voucher,
                ];
                $voucher_attachement = null;
                try {
                    $voucher_attachement = $this->vourcher_attach([$pdf_data]);
                    // return response([
                    //     'status' => -211,
                    //     'message' => 'test here ' . $voucher_attachement
                    // ]);
                } catch (Exception $e) {
                    return response([
                        'status' => -211,
                        'message' => $e->getMessage(),
                    ]);
                }
                if(Inventory::create($input)){
                    $ptn = Userinfo::where('internal_id', $request->get('partner_identity'))->first();
                    $usr = Userinfo::where('internal_id', $i->customer_user_id)->first();
                    $this_usr = User::find($usr->userid);
                    $cancellation_payload = [
                        'cancelled_voucher' => $voucher,
                        'new_voucher' => $new_box_voucher,
                        'reason' => $request->get('reason'),
                        'partner' => $request->get('partner_identity')
                    ];
                    Cancellation::create($cancellation_payload);
                    $payload = [
                        'message' => 'The voucher '.$voucher.' which you had redeemed at '.$ptn->business_name.' has been cancelled and replaced by a new voucher code ' .$new_box_voucher.'. You will need to login to your account and reactivate the new code.',
                        'voucher' => $new_box_voucher,
                        'partner' => $ptn->business_name,
                        'reason' => $request->get('reason'),
                        'name' => $this_usr->name,
                        'evoucher_attachment' => $voucher_attachement,
                    ];
                    $user = $this_usr->email;
                    Mail::to($user)->send(new PartnerCancellation($payload));
                    $admin_user = Config::get('mail.from.address');
                    // Mail::to($admin_user)->send(new PartnerCancellationAdmin($payload));
                    return response([
                        'status' => 0,
                        'message' => 'Voucher '.$voucher.' has been cancelled. The customer will be notified',
                        'voucher' => $new_box_voucher
                    ]);
                }
                return response([
                    'status' => -211,
                    'Action failed'
                ]);
            }
        }
        return response([
            'status' => -211,
            'message' => 'General error occured.',
            'voucher' => $voucher
        ]);
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
                'box_qty' => 'required',
                'box_voucher_status' => 'required|string',
                'box_delivery_address' => 'required|string'
            ]);
            if(!intval($request->get('box_qty')))
            {
                return response([
                    'status' => -211,
                    'message' => 'Invalid quantity: ' . $request->get('box_qty'),
                ], 401);
            }
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
