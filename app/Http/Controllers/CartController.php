<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ShippingCharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        $product = Product::with('product_images')->find($request->id);
    
        if ($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product Not Found'
            ]);
        }
    
        
    
        if (Cart::count() > 0) {
           // echo 'product in already in cart';
           //product found in cart
           //check if this product already in cart
           //return as message that product already added in your cart
           //if not product found in cart then add product in cart

           $cartContent=Cart::content();
           $productAlreadyExist=false;

           foreach ($cartContent as $item) {

            if($item->id == $product->id){
                $productAlreadyExist=true;
            } 

           }
            if($productAlreadyExist==false){

                Cart::add($product->id, $product->title, 1, $product->price, ['ProductImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status=true;
                $message=$product->title.' added in your cart';
                session()->flash('success',$message);

            }else{
                $status=false;
                $message=$product->title.'already added in cart';
            }

          
        } else {
           // echo 'cart is empty now product adding in your cart';
            Cart::add($product->id, $product->title, 1, $product->price, ['ProductImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status=true;
            $message='<b>'.$product->title.'</b> added in your cart';
            session()->flash('success',$message);
            
        }
    
        return response()->json([
            'status' => $status,
            'message' =>  $message
        ]);
    }
    

    public function cart(){
        //dd(Cart::content());
        $cartContent=Cart::content();
        //dd($cartContent);
        $data['cartContent']=$cartContent;
        return view('front.cart',$data);
    }

    public function updateCart(Request $request){
       $rowId=$request->rowId;
       $qty=$request->qty;
       // check for stock
       $itemInfo=Cart::get($rowId);
       $product=Product::find($itemInfo->id);
       if ($product->track_qty == 'yes') {
        
        if ($qty <= $product->qty) {
            Cart::update($rowId, $qty);
            $message = 'Cart updated successfully!';
            $status = true;
            session()->flash('success',$message);
        } else {
            $message = 'Requested Qty (' . $qty . ') not available in stock. Available: ' . $product->qty;
            $status = false;
            session()->flash('error',$message);
        }
      } else {
        Cart::update($rowId, $qty);
        $message = 'Cart updated successfully!';
        $status = true;
        session()->flash('success',$message);
      }
     
      return response()->json([
       'status' => $status,
       'message' => $message
     ]);
        
    }

    public function deleteItem(Request $request){
        $itemInfo=Cart::get($request->rowId);
        if($itemInfo == null){
            $errormessage='Item not found in cart';
            session()->flash('error',$errormessage);
            return response()->json([
                'status' => false,
                'message' => $errormessage
            ]);
        }
        Cart::remove($request->rowId);
        $message = 'Item removed from cart successfully!';
        session()->flash('success',$message);
        return response()->json([
            'status' => false,
            'message' => $message
        ]);
    }

// new data start
public function checkout(Request $request){
    
    $discount=0;
    if (Cart::count() == 0) {
        return redirect()->route('front.cart')->with('error', 'Your cart is empty.');
    }

    if (!Auth::check()) {
        //dd(Auth::user(), session()->all());
        if (!session()->has('url.intended')) {
            session(['url.intended' => route('front.checkout')]);
        }
        return redirect()->route('accounts.login');
    }
  
    $user = Auth::user();
    $customerAddresses = CustomerAddress::where('user_id', $user->id)->first();
    session()->forget('url.intended');  
    $countries = Country::orderBy('name', 'ASC')->get();
    $subtotal = Cart::subtotal(2, '.', '');

    // set country id
    $userCountry = $customerAddresses ? $customerAddresses->country_id : null;

    //  Get Shipping Charge
    $shippingInfo = $userCountry ? ShippingCharge::where('country_id', $userCountry)->first() : null;

    if(session()->has('code')){
        $code = session()->get('code');
        if($code->type == 'percent'){
           $discount = ($code->discount_amount/100)*$subtotal;
        }else{
           $discount = $code->discount_amount;
        }
    }

    // Shipping Calculation
    $totalShippingCharge = 0;
    $totalQty = 0;

    foreach (Cart::content() as $item) {
        $totalQty += $item->qty;
    }

    $totalShippingCharge = $shippingInfo ? ($totalQty * floatval($shippingInfo->amount)) : ($totalQty * 30);
    $grandtotal = floatval($subtotal-$discount) + $totalShippingCharge;

    return view('front.checkout', [
        'countries' => $countries,
        'customerAddress' => $customerAddresses,
        'totalShippingCharge' => $totalShippingCharge,
        'discount' => $discount,
        'grandtotal' => $grandtotal,
    ]);
}

    //new data start 
    public function processCheckout(Request $request){
        $discount=0;
        $details = $request->validate([
            'first_name' =>'required',
            'last_name' =>'required',
            'email' => 'required|email',
            'country' =>'required|exists:countries,id',
            'address' =>'required',
            'city' =>'required',
            'state' =>'required',
            'zip' =>'required',
            'mobile' =>'required',
        ]);
    
        if(!$details) {
            return response()->json([
                'message' => 'Please fix the errors',
                'status' => false,
                'errors' => $details,
            ]);
        }
    
        // User Address Save/Update 
        $user = Auth::user();
    
        CustomerAddress::updateOrCreate(
           ['user_id' => $user->id],
           [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'country_id' => $request->country,
            'address' => $request->address,
            'apartment' => $request->apartment,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'notes' => $request->order_notes ?? '',
           ]
        );
    
        if ($request->payment_method == 'cod') {
            // calculate shipping charge
            $shipping = 0;
            $subtotal = floatval(Cart::subtotal(2, '.', ''));
            $discountCodeId=NULL;
            $promoCode='';

            //apply discount
            if(session()->has('code')){
                $code = session()->get('code');
                if($code->type == 'percent'){
                   $discount = ($code->discount_amount/100)*$subtotal;
                }else{
                   $discount = $code->discount_amount;
                }

                $discountCodeId=$code->id;
                $promoCode=$code->code;

                
            }
    
            $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();
            $totalQty = Cart::count();
    
            if ($shippingInfo) {
                $shipping = $totalQty * floatval($shippingInfo->amount);
            } else {
                $shipping = $totalQty * 30; // Default Shipping Charge
            }
    
            $grandtotal = ($subtotal-$discount) + $shipping;
    
            // save order
            $order = new Order();
            $order->subtotal = $subtotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandtotal;
            $order->discount = $discount;
            $order->coupone_code_id = $discountCodeId;
            $order->coupone_code = $promoCode;
            $order->payment_status = 'unpaid';
            $order->order_status = 'pending';
 
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes ?? '';
            $order->save();

    
            // save order Item
            foreach (Cart::content() as $item) {
                $orderItem = new OrderItems();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->save();

                $productData = Product::find($item->id);
                if($productData->track_qty == 'yes'){
                    $currentQty=$productData->qty;
                    $updatedQty=$currentQty-$item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();

                }
            }

            // send order email
            orderEmail($order->id,'customer');
    
            
            session()->flash('success', 'You Have Successfully Placed Your Order');
            Cart::destroy();
            session()->forget('code');
    
            return response()->json([
                'message' => 'Order saved successfully!',
                'orderId' => $order->id,
                'status' => true,
            ]);
        }
    }
    
    //new data end

    public function thankyou($id){
        return view('front.thanks',['id' => $id]);
    }

    public function getOrderSummery(Request $request){
        $subtotal = floatval(Cart::subtotal(2, '.', ''));
        $discount=0;
        $discountString = '';
        // apply discount

        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
               $discount = ($code->discount_amount/100)*$subtotal;
            }else{
               $discount = $code->discount_amount;
            }
            $discountString = '<div class="mt-4" id="discount-response">
            <strong>'.session()->get('code')->code.'</strong>
            <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>   
            </div>'; 

        }

        
        
        if ($request->country_id > 0) {
            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();
            $totalQty = Cart::count();
    
            if ($shippingInfo) {
                $shippingCharge = $totalQty * floatval($shippingInfo->amount);
            } else {
                $shippingCharge = $totalQty * 30; // Default Shipping Charge
            }
    
            $grandtotal = ($subtotal-$discount) + $shippingCharge;
    
            return response()->json([
                'status' => true,
                'grandtotal' => number_format($grandtotal, 2),
                'discount' => number_format($discount , 2),
                'discountString' => $discountString,
                'shippingCharge' => number_format($shippingCharge, 2),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'grandtotal' => number_format(($subtotal-$discount), 2),
                'discount' => number_format($discount , 2),
                'discountString' => $discountString,
                'shippingCharge' => number_format(0, 2),
            ]);
        }
    }

    
    public function applyDiscount(Request $request){
        $code = DiscountCoupon::where('code', $request->code)->first();
        if ($code == null) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon',
            ]);
        }
    
        $now = Carbon::now()->setTimezone(config('app.timezone'));
    
        if (!empty($code->starts_at)) {
            $startDate = Carbon::parse($code->starts_at)->setTimezone(config('app.timezone'));
    
          
    
            if ($now->lt($startDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon start',
                ]);
            }
        }
    
        if (!empty($code->expires_at)) {
            $endDate = Carbon::parse($code->expires_at)->setTimezone(config('app.timezone'));
            if ($now->gt($endDate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon end',
                ]);
            }
        }

        //max uses check
        if($code->max_uses > 0){
            $couponUsed=Order::where('coupone_code_id',$code->id)->count();
            if($couponUsed >= $code->max_uses){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon of max uses',
                ]);
            }
        }
       

        //max uses user check
        if($code->max_uses_user > 0){
            $couponUsedByUser=Order::where(['coupone_code_id' => $code->id,'user_id' => Auth::user()->id])->count();
            if($couponUsedByUser >= $code->max_uses_user){
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon',
                ]);
            }
        
        }

        $subtotal = floatval(Cart::subtotal(2, '.', ''));
        //min amount condition check

        if($code->min_amount > 0){
            if($subtotal < $code->min_amount){
                return response()->json([
                    'status' => false,
                    'message' => 'Your min Amount must be ₹'.$code->min_amount.'.',
                ]);
            }
        }



        
        session()->put('code', $code);
        return $this->getOrderSummery($request);
    }

    public function removeDiscount(Request $request){
        session()->forget('code');
        return $this->getOrderSummery($request);

    }
    
}
