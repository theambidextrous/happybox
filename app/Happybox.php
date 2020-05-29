<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Happybox extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'internal_id', 'name', 'price', 'description', 'topics', 'is_active', 'voucher', 'stock',
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
