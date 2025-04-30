<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request){
        $orders=Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders=$orders->leftJoin('users','users.id','orders.user_id');
        if($request->get('keyword')){
         $orders->where('users.name','like','%'.$request->keyword.'%');
         $orders->orwhere('users.email','like','%'.$request->keyword.'%');
         $orders->orwhere('orders.id','like','%'.$request->keyword.'%');
        }
        $orders=$orders->paginate(10);
        $data['orders']=$orders;
        return view('admin.orders.order',$data);
    }

    public function orderdetail($orderId){
        $order=Order::select('orders.*','countries.name as countryName')
                     ->where('orders.id',$orderId)
                     ->leftJoin('countries','countries.id','orders.country_id')
                     ->first();
        $orderItems=OrderItems::where('order_id',$orderId)->get();
        $data['orderItems']=$orderItems;
        $data['order']=$order;
        return view('admin.orders.order-details',$data);
    }

    public function changeOrderStatus(Request $request,$orderId){
       $order=Order::find($orderId);
       $order->order_status = $request->order_status;
       $order->shipped_date = $request->shipped_date;
       $order->save();
       $message='Order Status Updated Successfully';
       session()->flash('success',$message);
       return response()->json([
        'status' => true,
        'message' => $message,
       ]);
    }

    public function sendInvoiceEmail(Request $request,$orderId){

        orderEmail($orderId,$request->userType);
        $message='Invoice Email Sent Successfully';
        session()->flash('success',$message);
        return response()->json([
          'status'=>true,
          'message'=>$message
        ]);
    }
}
