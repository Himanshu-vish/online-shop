<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(){
      return view('front.accounts.register');
    }

    public function login(){
      return view('front.accounts.login');
    }

    public function processRegister(Request $request){
      $details=$request->validate([
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users',
        'password'=>'required|min:5|confirmed'
    ]);
    if ($details){

      $user = new User();
      $user->name = $request->name;
      $user->email = $request->email;
      $user->phone = $request->phone;
      $user->password =Hash::make($request->password) ;
      
      $user->save();

      session()->flash('success','Registration successfully');
      return response()->json([
        'status'=> true,
       
    ]);
    }else{
      return response()->json([
          'status'=> false,
          'errors' => $details,
      ]);
     }
    }

    public function authenticate(Request $request){
      $details=$request->validate([
        'email' => 'required|email',
        'password'=>'required'
      ]);
      
      if($details){

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember'))){
            if (session()->has('url.intended')) {
                return redirect(session()->get('url.intended'));
            }
            return redirect()->route('accounts.profile')->with('success', 'Successfully logged in!');
        }else{
          return redirect()->route('accounts.login')
            ->withErrors(['email' => 'Sorry Your Email/Password is incorrect!'])
            ->withInput($request->only('email'));
        }

      }else{
         return redirect()->route('accounts.login')
            ->withErrors($details)
            ->withInput($request->only('email'));
      }
    } 

    public function profile(){
      $countries = Country::orderBy('name','ASC')->get();
      $user=User::where('id',Auth::user()->id)->first();
      $address=CustomerAddress::where('user_id',Auth::user()->id)->first();
      $data['user']=$user;
      $data['countries']=$countries;
      $data['address']=$address;
      return view('front.accounts.profile',$data);
    }

    public function updateProfile(Request $request){
      $userId=Auth::user()->id;
      $details=$request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email,'.$userId.',id',
        'phone'=>'required'
      ]);

      if($details){
          $user = User::find($userId);
          $user->name = $request->name;
          $user->email = $request->email;
          $user->phone = $request->phone;
          $user->save();
          session()->flash('success','Profile Updated Successfully!');
          return response()->json([
            'status' => true,
            'message' => 'Profile Updated Successfully!',
        ]);

      }else{
        return response()->json([
            'status' => false,
            'errors' => $details,
        ]);
      }
    }

    public function updateAddress(Request $request){
    
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

      if($details){
        $userId=Auth::user()->id;
        CustomerAddress::updateOrCreate(
          ['user_id' => $userId],
          [
           'user_id' => $userId,
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
          session()->flash('success','Address Updated Successfully!');
          return response()->json([
            'status' => true,
            'message' => 'Address Updated Successfully!',
        ]);

      }else{
        return response()->json([
            'status' => false,
            'errors' => $details,
        ]);
      }
    }

    public function logout(Request $request){
      Auth::logout();
      $request->session()->invalidate(); 
      $request->session()->regenerateToken();
      return redirect()->route('accounts.login')->with('success','You Successfully logout !');
    }

    public function orders(){
      $user = Auth::user();
      $orders=Order::where('user_id',$user->id)->orderBy('created_at','DESC')->get();
      $data['orders']=$orders;
      return view('front.accounts.order',$data);
    }

    public function orderdetails($id){
      $user = Auth::user();
      $orderDetails=Order::where('user_id',$user->id)->where('id',$id)->first();
      $data['orderDetails']=$orderDetails;
      $orderItems=OrderItems::where('order_id',$id)->get();
      $data['orderItems']=$orderItems;
      $orderItemsCount=OrderItems::where('order_id',$id)->count();
      $data['orderItemsCount']=$orderItemsCount;

      return view('front.accounts.order-details',$data);
    }

    public function wishlist(){
      $wishlists=Wishlist::where('user_id',Auth::user()->id)->with('product')->get();
      $data['wishlists']=$wishlists;
      return view('front.accounts.wishlist',$data);
    }

    public function removeProductFromWishlist(Request $request){
      $wishlist=Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id);
      if($wishlist == null){
        session()->flash('erorr', 'Product Already removed');
        return response()->json([
          'status' => true,
        ]);
      }else{

         Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
         session()->flash('success', 'Product removed successfully!');
         return response()->json([
           'status' => true,
         ]);
      }
    }

    public function showChangePassword(){
         return view('front.accounts.change-password');
    }

    public function changePassword(Request $request){
      
      $details=$request->validate([
        'old_password' => 'required',
        'new_password' => 'required|min:5',
        'confirm_password' => 'required||same:new_password',
      

      ]);
      if($details){

        $user=User::select('id','password')->where('id',Auth::user()->id)->first();
        if(!Hash::check($request->old_password,$user->password)){
          session()->flash('error','Your Old Password is Incorrect , Please Try Agian ');
          return response()->json([
             'status' => true
          ]);
        }
        User::where('id',$user->id)->update([
           'password' => Hash::make($request->new_password)
        ]);
        session()->flash('success','Your Password Changed SuccessFully!');
        return response()->json([
           'status' => true,
           'message' => 'Your Password Changed SuccessFully!',
        ]);

      }else{
        return response()->json([
           'status' => false,
           'errors' => $details
        ]);
      }
  }

  public function forgotPassword(){
     return view('front.accounts.forgot-password');
  }

  public function processForgotPassword(Request $request){
 
    $details=$request->validate([
      'email' => 'required|email|exists:users,email',
    ]);

    if($details){

    }else{
      return redirect()->route('front.forgotPassword')->withInput()->withErrors($details);
    }

    $token = Str::random(60);

    DB::table('password_resets')->where('email',$request->email)->delete();

    DB::table('password_resets')->insert([
      'email' => $request->email,
      'token' => $token,
      'created_at' => now()
    ]);


    // send email 

    $user = User::where('email',$request->email)->first();
    $formData = [
      'token' => $token,
      'user' => $user,
      'mailSubject' => 'You Have requested to reset password',
    ];
    Mail::to($request->email)->send(new ResetPassword($formData));
    return redirect()->route('front.forgotPassword')->with('success','Please check your inbox to reset your password');
  }

  public function resetPassword($token){
    $tokenExist= DB::table('password_resets')->where('token',$token)->first();
    if($tokenExist == null){
      return redirect()->route('front.forgotPassword')->with('error','Inavalid Request');
    }

    return view('front.accounts.reset-password',[
      'token' => $token,
    ]);
  }

  public function processResetPassword(Request $request){
       $token=$request->token;
       $tokenObj= DB::table('password_resets')->where('token',$token)->first();
       if($tokenObj == null){
          return redirect()->route('front.forgotPassword')->with('error','Inavalid Request');
       }

       $user = User::where('email',$tokenObj->email)->first();
       $details=$request->validate([
        'new_password' => 'required|min:5',
        'confirm_password' => 'required|same:new_password',
      ]);
  
      if($details){
        User::where('id',$user->id)->update([
           'password' => Hash::make($request->new_password)
        ]);
        DB::table('password_resets')->where('email',$user->email)->delete();
        return redirect()->route('accounts.login')->with('success','You SuccessFully changed Password');
      }else{
        return redirect()->route('front.resetPassword',$token)->withInput()->withErrors($details);
      }
  }
}
