<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DiscountCodeController extends Controller
{
    public function index(Request $request){
        $discountcoupons=DiscountCoupon::latest();
        if (!empty($request->get( 'keyword'))){
            $discountcoupons->where('name','like','%'.$request->get( 'keyword').'%');
        }
        $discountcoupons=$discountcoupons->paginate(10);
        //dd($categories);
        //$data['categories']=$categories;
        return view('admin.coupons.list',compact('discountcoupons'));//$data
    }

    public function create(){
     return view('admin.coupons.create');
    }

  
    public function store(Request $request) {
        $details = $request->validate([
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',    
        ]);
    
        if ($details) {
            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startsAt = Carbon::parse($request->starts_at)->setTimezone(config('app.timezone'));
    
    
                if ($startsAt->gte($now)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => ['Start date must be in the future.']]
                    ], 422);
                }
            }
    
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $startsAt = Carbon::parse($request->starts_at)->setTimezone(config('app.timezone'));
                $expiresAt = Carbon::parse($request->expires_at)->setTimezone(config('app.timezone'));
    
                if ($expiresAt->lt($startsAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => ['Expiry invalid date time']]
                    ], 422);
                }
            }
    
            $discount = new DiscountCoupon();
            $discount->code = $request->code;
            $discount->name = $request->name;
            $discount->discription = $request->discription;
            $discount->max_uses = $request->max_uses;
            $discount->max_uses_user = $request->max_uses_user;
            $discount->type = $request->type;
            $discount->discount_amount = $request->discount_amount;
            $discount->min_amount = $request->min_amount;
            $discount->status = $request->status;
            $discount->starts_at = Carbon::parse($request->starts_at)->format('Y-m-d H:i:s');
            $discount->expires_at = Carbon::parse($request->expires_at)->format('Y-m-d H:i:s');
            $discount->save();
    
            $message = 'Discount Coupon created Successfully!';
            session()->flash('success', $message);
    
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $details
            ]);
        }
    }
    

    public function edit($couponID,Request $request){
        $discountcoupon=DiscountCoupon::find($couponID);
        if(empty($discountcoupon)){
            redirect()->route('coupon.index');
         }
        return view('admin.coupons.edit',compact('discountcoupon'));
     
       }


    public function update($couponID,Request $request){
        $discount=DiscountCoupon::find($couponID);
        if(empty($discount)){
            $request->session()->flash('error','record not found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
                'message'=>'record not found',
            ]);
        }
        $details=$request->validate([
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',   
        ]);
        if ($details){
            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $startsAt = Carbon::parse($request->starts_at)->setTimezone(config('app.timezone'));
    
    
                if ($startsAt->gte($now)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => ['Start date must be in the future.']]
                    ], 422);
                }
            }
    
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $startsAt = Carbon::parse($request->starts_at)->setTimezone(config('app.timezone'));
                $expiresAt = Carbon::parse($request->expires_at)->setTimezone(config('app.timezone'));
    
                if ($expiresAt->lt($startsAt)) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expires_at' => ['Expiry invalid date time']]
                    ], 422);
                }
            }
    
            
            $discount->code = $request->code;
            $discount->name = $request->name;
            $discount->discription = $request->discription;
            $discount->max_uses = $request->max_uses;
            $discount->max_uses_user = $request->max_uses_user;
            $discount->type = $request->type;
            $discount->discount_amount = $request->discount_amount;
            $discount->min_amount = $request->min_amount;
            $discount->status = $request->status;
            $discount->starts_at = Carbon::parse($request->starts_at)->format('Y-m-d H:i:s');
            $discount->expires_at = Carbon::parse($request->expires_at)->format('Y-m-d H:i:s');
            $discount->save();
    
            $message = 'Discount Coupon Updated Successfully!';
            session()->flash('success', $message);
    
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $details
            ]);
        }
    }    

    public function destroy($couponID,Request $request){
        $discountcoupon=DiscountCoupon::find($couponID);
        if(empty($discountcoupon)){
            $request->session()->flash('error','coupon not found');
            return response()->json([
                'status'=>true,
                'message'=>'coupon not found',
            ]);
        }
        
        $discountcoupon->delete();
        $request->session()->flash('success','Coupon deleted successfully !');
        return response()->json([
            'status'=>true,
            'message'=>'Coupon deleted successfully !',
        ]);
       }
}
