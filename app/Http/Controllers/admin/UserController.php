<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request){
        $users=User::latest();
        
        if($request->get('keyword')){
            $users=$users->where('name','like','%'.$request->get('keyword').'%');
            $users=$users->Orwhere('email','like','%'.$request->get('keyword').'%');
        }
        $users=$users->paginate(10);
        $data['users']=$users;
        return view('admin.users.list',$data);
    }

    public function create(){
        return view('admin.users.create');
    }

    public function store(Request $request){
        $details=$request->validate([
            'name' => 'required',
            'password' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'phone' => 'required',

        ]);
        if($details){

            $user=new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->name);
            $user->status = $request->status;
            $user->save();
            session()->flash('success', 'User Created SuccessFully!');
            return response()->json([
              'status' => true,
              'message' => 'User Created SuccessFully!'
            ]);

        }else{
            return response()->json([
               'status' => false,
               'errors' => $details
            ]);
        }

    }

    public function edit(Request $request,$userID){
        $users=User::find($userID);
        if(empty($users)){
            redirect()->route('users.index');
         }
         $data['users']=$users;
        return view('admin.users.edit',$data);

    }

    public function update($usersID,Request $request){
        $user=User::find($usersID);
        if(empty($user)){
            $request->session()->flash('error','record not found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
                'message'=>'record not found',
            ]);
        }
        $details=$request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required'
        ]);
        if ($details){ 
            $user->name=$request->name;
            $user->email=$request->email;
            $user->phone=$request->phone;
            $user->status=$request->status;
            $user->save();
            $request->session()->flash('success','Record Updated !');
            return response()->json([
                'status'=> true,
                'message' => 'Record Updated !',
            ]);
        } else {
                return response()->json([
                'status'=> false,
                'errors' => $details,
            ]);
        }

    }

    public function destroy($userID,Request $request){
        $user=User::find($userID);
        if(empty($user)){
            $request->session()->flash('error','User not found');
            return response()->json([
                'status'=>true,
                'message'=>'User not found',
            ]);
        }
        
        $user->delete();
        $request->session()->flash('success','User deleted successfully !');
        return response()->json([
            'status'=>true,
            'message'=>'User deleted successfully !',
        ]);
    }
}
