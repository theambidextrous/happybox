<?php

namespace App\Http\Controllers\api\v0\happybox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rating;
use Validator;
use Auth;
use Config;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Str;

class RatingController extends Controller
{
    public function index()
    {
        try{
            $i =  Rating::all();
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
    public function create(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'rating_user' => 'required|string',
                'rating_value' => 'required|integer',
                'comment' => 'required|string',
                'partner' => 'required|string'
            ]);
            if( $validator->fails() ){
                return response([
                    'status' => -211,
                    'message' => 'Invalid or empty field',
                    'errors' => $validator->errors()
                ], 401);
            }
            $input = $request->all();
            $r = Rating::create($input);
            return response([
                'status' => 0,
                'message' => 'created successfully',
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
    public function by_ptn_value($idf)
    {
        try {
            $r =  Rating::where('partner', $idf)->sum('rating_value');
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $r
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
    public function by_ptn($idf)
    {
        try {
            $r =  Rating::where('partner', $idf)->get();
            return response([
                'status' => 0,
                'message' => 'fetched successfully',
                'data' => $r
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
}
