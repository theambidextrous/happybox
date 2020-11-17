<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'customer_buyer', 'order_evouchers', 'order_pvouchers','order_string', 'subtotal', 'shipping_cost', 'order_totals', 'paid', 'payment_method', 'payment_status', 'payment_string','shipped', 'shipment_status', 'shipment_string', 'token','checkout_request_id','paid_amount',
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
