<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Page;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

function getCategories(){
    return Category::orderBy('name','ASC')
                      ->with('sub_category')
                      ->orderBy('id','DESC')->where('ShowHome','Yes')->get();
}

function getProductImage($productId){
    return ProductImage::where('product_id',$productId)->first();
}

function orderEmail($orderId,$userType='customer'){
    $order = Order::where('id',$orderId)->with('items')->first();

    if($userType == 'customer'){
       $subject= 'Thanks For Your Order';
       $email=$order->email;
    }else{
        $subject= 'You Have Received An Order';
        $email=env('ADMIN_EMAIL');
    }
    $mailData=[
        'subject' => 'Thanks For Your Order',
        'order' => $order,
        'userType' => $userType,
    ];

    Mail::to($email)->send(new OrderEmail($mailData));
    //dd($order);
}

function getCountryInfo($id){
    return Country::where('id',$id)->first();

}

function staticPage(){
   $pages = Page::orderBy('name','ASC')->get();
   return $pages;
}

?>