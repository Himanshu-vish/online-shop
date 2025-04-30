<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ShippingController extends Controller
{
   public function create(){
     $countries=Country::get();
     $data['countries']=$countries;
     $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
                                     ->leftJoin('countries','countries.id','shipping_charges.country_id')
                                     ->get();
     $data['shippingCharges']=$shippingCharges;                                
     return view('admin.shipping.create',$data);
   }

   public function store(Request $request){
            $details=$request->validate([
                'country' => 'required',
                'amount' => 'required|numeric',
            ]);
            if ($details){
                $count = ShippingCharge::where('country_id',$request->country)->count();
                if($count > 0){
                    $request->session()->flash('error','Shipping Already Added !');
                    return response()->json([
                        'status'=> false,
                        'message' => 'Shipping Added successfully !',
                    ]);
                }
                $shipping = new ShippingCharge();
                $shipping->country_id = $request->country;
                $shipping->amount = $request->amount;
                $shipping->save();
            
                $request->session()->flash('success','Shipping Added successfully !');
                return response()->json([
                    'status'=> true,
                    'message' => 'Shipping Added successfully !',
                ]);
            }else{
                return response()->json([
                    'status'=> false,
                    'errors' => $details,
                ]);

            }
   }

   public function edit($id){
    $shippingCharges=ShippingCharge::find($id);
    $countries=Country::get();
    $data['countries']=$countries;
    $data['shippingCharges']=$shippingCharges;
    return view('admin.shipping.edit',$data);
   }

   public function update($id,Request $request){
   
    $details=$request->validate([
        'country' => 'required',
        'amount' => 'required|numeric',
    ]);
    if ($details){

        $shipping=ShippingCharge::find($id);
        $shipping->country_id = $request->country;
        $shipping->amount = $request->amount;
        $shipping->save();
    
        $request->session()->flash('success','Shipping Updated successfully !');
        return response()->json([
            'status'=> true,
            'message' => 'Shipping Updated successfully !',
        ]);
    }else{
        return response()->json([
            'status'=> false,
            'errors' => $details,
        ]);

    }
   }

   public function destroy($id,Request $request){
    $shippingCharges=ShippingCharge::find($id);
    if($shippingCharges == null){
        $request->session()->flash('error','Shipping not found');
        return response()->json([
            'status'=> true,
            'message' => 'Shipping Updated successfully !',
        ]); 
    }
    $shippingCharges->delete();
    $request->session()->flash('success','Shipping deleted successfully !');
        return response()->json([
            'status'=> true,
            'message' => 'Shipping deleted successfully !',
        ]); 
   }
}
