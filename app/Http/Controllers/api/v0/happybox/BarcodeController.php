<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function barcode()
    {
        return view('barcode');
    }
    
}
