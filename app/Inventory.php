<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_buyer_id', 'customer_payment_method', 'box_delivery_address', 'box_purchase_date', 'box_validity_date', 'customer_buyer_invoice', 'box_barcode', 'box_internal_id','box_voucher','box_voucher_new','box_voucher_status','voucher_activation_date','customer_user_id','redeemed_date','cancellation_date','booking_date','partner_pay_due_date','partner_pay_effec_date','partner_pay_amount','partner_internal_id','partner_invoice','box_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];
}
