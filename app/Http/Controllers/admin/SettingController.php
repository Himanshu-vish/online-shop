<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function showChangePasswordForm(){
        return view('admin.change-password');
    }

    public function changePassword(Request $request){

        $details=$request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required||same:new_password',
          
    
          ]);
          if($details){
    
            $admin=User::where('id',Auth::guard('admin')->user()->id)->first();
            if(!Hash::check($request->old_password,$admin->password)){
              session()->flash('error','Your Old Password is Incorrect , Please Try Agian ');
              return response()->json([
                 'status' => true
              ]);
            }
            User::where('id',Auth::guard('admin')->user()->id)->update([
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
}
