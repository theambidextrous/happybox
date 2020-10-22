<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderCron extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'customer_buyer', 'box_voucher', 'order_meta', 'deliver_to', 'sendy_log', 'is_send',
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
