<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnSelf;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }
    public function authenticate(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if(Auth::guard('admin')->attempt($credentials)){
            $admin = Auth::guard('admin')->user();
           // dd($admin); // print admin data
    
            if($admin->role == 2){
                return redirect()->route('admin.dashboard');
            } else {
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error','You are not Authorized to access Admin Panel');
            }
        } else {
            return redirect()->route('admin.login')->with('error','Invalid Email or Password');
        }
    }
    
    public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success','You have successfully logged out!');
    }
}
