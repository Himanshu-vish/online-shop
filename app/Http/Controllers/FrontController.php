<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Mail\ContactEmail;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FrontController extends Controller
{
    public function index(){
        $featureProduct=Product::where('is_feature','Yes')
                             ->orderBy('id','DESC')
                             ->where('Status','1')
                             ->take(8)->get();
        $data['FeatureProduct']=$featureProduct;

        $latestProduct=Product::orderBy('id','DESC')
                                ->where('Status','1')
                                ->take(8)
                                ->get();
        $data['latestProduct']=$latestProduct;
        return view('front.home',$data);
    }

    public function addToWishlist(Request $request){
        
        if(Auth::check() == false){
            session(['url.intended' => route('front.home')]);
            return response()->json([
               'status' => false,
            ]);
        }

        $product=Product::where('id',$request->id)->first();

        if($product == null){
            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-success">record not found.</div>'
            ]);
                
        }

        Wishlist::updateOrCreate(
            [
            'user_id' => Auth::user()->id,
            'product_id' =>  $request->id,
            ],
            ['user_id' => Auth::user()->id,
            'product_id' =>  $request->id,
            ]
        );
        // $wishlist = new Wishlist();
        // $wishlist->user_id = Auth::user()->id;
        // $wishlist->product_id=$request->id;
        // $wishlist->save();
        
        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success">'.$product->title.' added to your wishlist.</div>'
        ]);
        
    }

    public function page($slug){
        $pages=Page::where('slug',$slug)->first();
        $data['pages']=$pages;
        return view('front.page',$data);
    }

    public function sendContactEmail(Request $request){

        $details=$request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
        ]);

        if($details){

            $mailData=[
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject'=> 'You Have received a contact email'
            ];
            $admin=User::where('id',1)->first();
            Mail::to($request->email)->send(new ContactEmail($mailData));
            session()->flash('success','Thanks For Contacting Us!');
            return response()->json([
               'status' => true,
               'message' => 'Thanks For Contacting Us!',
            ]);
        }else{
            return response()->json([
               'status' => false,
               'errors' => $details,
            ]);
        }

    }
}
