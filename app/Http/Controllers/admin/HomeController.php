<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class HomeController extends Controller
{
     public function index(){

        $totalOrder=Order::where('order_status','!=','cancelled')->count();
        $totalCustomer=User::where('role',1)->count();
        $totalProduct=Product::count();

        // total sale
        $totalRevenue=Order::where('order_status','!=','cancelled')->sum('grand_total');

        // total this month sale
        $startOfMonth=Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentDate=Carbon::now()->format('Y-m-d');
        $revenueThisMonth=Order::where('order_status','!=','cancelled')
                                 ->whereDate('created_at','>=',$startOfMonth)
                                 ->whereDate('created_at','<=',$currentDate)
                                 ->sum('grand_total');
                                 
        //total last month sale
        $lastMonthStartDate=Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthName=Carbon::now()->subMonth()->startOfMonth()->format('M');
        $lastMonthEndDate=Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $revenuelastMonth=Order::where('order_status','!=','cancelled')
                                 ->whereDate('created_at','>=',$lastMonthStartDate)
                                 ->whereDate('created_at','<=',$lastMonthEndDate)
                                 ->sum('grand_total');

        // total sale of last 30 days                                 
        $revenueThirtyDayStartDate=Carbon::now()->subDay(30)->format('Y-m-d');
        $revenueLastThirtyDay=Order::where('order_status','!=','cancelled')
                                 ->whereDate('created_at','>=',  $revenueThirtyDayStartDate)
                                 ->whereDate('created_at','<=',$currentDate)
                                 ->sum('grand_total');


        $data['totalOrder']=$totalOrder;
        $data['totalCustomer']=$totalCustomer;
        $data['totalProduct']=$totalProduct;
        $data['totalRevenue']= $totalRevenue;
        $data['revenueThisMonth']=$revenueThisMonth;
        $data['revenuelastMonth']=$revenuelastMonth;
        $data['revenueLastThirtyDay']=$revenueLastThirtyDay;
        $data['lastMonthName']=$lastMonthName;
        return view('admin.dashboard',$data);
        // $admin=Auth::guard('admin')->user();
        // echo "welcome".$admin->name.'<a href="'.route('admin.logout').'">logout</a>';
     }

     public function logout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
     }
}
