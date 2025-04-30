<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;

class BrandsController extends Controller
{

    public function index(Request $request){
       $brands=Brands::latest('id');
       if($request->get('keyword')){
        $brands->where('name','like','%'.$request->keyword.'%');
       }
       $brands=$brands->paginate(10);
       return view('admin.brands.list',compact('brands'));
    }

    public function create(){
        return view('admin.brands.create');
    }


    public function store(Request $request){
        $details=$request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);
        if ($details){

            $brands = new Brands();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->status = $request->status;
            $brands->save();
            $request->session()->flash('success','Brands Added successfully !');
            return response()->json([
                'status'=> true,
                'message' => 'Brands Added successfully !',
            ]);
        }else{
            return response()->json([
                'status'=> false,
                'errors' => $details,
            ]);

     }
     }

    public function edit($id,Request $request){
        $brands=Brands::find($id);
        if(empty($brands)){
            $request->session()->flash('error','Record Not Found');
            return redirect()->route('brands.index');
        }
        // $categories=Category::orderBy('name','ASC')->get();
        // $data['categories']=$categories;
        $data['brands']=$brands;
        return view('admin.brands.edit',$data);
    }

    

    public function update($id,Request $request){
        $brands=Brands::find($id);
        if(empty($brands)){

            $request->session()->flash('error','Record Not Found');
            return response([
                'status'=>false,
                'notFound'=>true,
            ]);
        }
        $details=$request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brands->id.',id',
            'status'=> 'required',
        ]);
        if($details){

            
            $brands->name=$request->name;
            $brands->slug=$request->slug;
            $brands->status=$request->status;
            $brands->save();
            
            $request->session()->flash('success','Brand Updated successfully !');
            return response()->json([
                'status'=> true,
                'message' => 'Brand Updated successfully !',
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'errors' => $details,
            ]);
        }

    }

    public function destroy($ID,Request $request){
        $brands=Brands::find($ID);
                if(empty($brands)){
                    $request->session()->flash('error','Record not found');
                    return response()->json([
                        'status'=>true,
                        'notFound'=>'true',
                    ]);
                }
                $brands->delete();
                $request->session()->flash('success','Brands deleted successfully !');
                return response()->json([
                        'status'=>true,
                        'message'=>'Brands deleted successfully !',
                   ]);
    }
}
