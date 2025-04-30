<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{

    public function index(Request $request){
        $sub_categories=SubCategory::select('sub_categories.*','categories.name as categoryName')
        ->latest('sub_categories.id')
        ->leftJoin('categories','categories.id','sub_categories.category_id');

        // for searching code 
        if (!empty($request->get( 'keyword'))){
            $sub_categories->where('sub_categories.name','like','%'.$request->get( 'keyword').'%');
            $sub_categories->orwhere('categories.name','like','%'.$request->get( 'keyword').'%');

        }

        $sub_categories=$sub_categories->paginate(10);
        //dd($categories);
        //$data['categories']=$categories;
        return view('admin.sub_category.list',compact('sub_categories'));//$data
       }

    // public function create(){
    //     return view('admin.category.create');
    //    }   
   

    public function create(){
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        return view('admin.sub_category.create',$data);
    }

    public function store(Request $request){
        $details=$request->validate([
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category'=>'required',
            'status'=> 'required',
        ]);
        if($details){

            $sub_category = new SubCategory();
            $sub_category->name=$request->name;
            $sub_category->slug=$request->slug;
            $sub_category->category_id=$request->category;
            $sub_category->status=$request->status;
            $sub_category->ShowHome=$request->ShowHome;
            $sub_category->save();
            
            $request->session()->flash('success','Sub-Category Added successfully !');
            return response()->json([
                'status'=> true,
                'message' => 'Sub-Category Added successfully !',
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'errors' => $details,
            ]);
        }
    }

    public function edit($id,Request $request){
        $sub_category=SubCategory::find($id);
        if(empty($sub_category)){
            $request->session()->flash('error','Record Not Found');
            return redirect()->route('sub-categories.index');
        }
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['sub_category']=$sub_category;
        return view('admin.sub_category.edit',$data);
    }

    public function update($id,Request $request){
            $sub_category=SubCategory::find($id);
            if(empty($sub_category)){

                $request->session()->flash('error','Record Not Found');
                return response([
                    'status'=>false,
                    'notFound'=>true,
                ]);
            }
            $details=$request->validate([
                'name' => 'required',
                //'slug' => 'required|unique:sub_categories',
                'slug' => 'required|unique:sub_categories,slug,'.$sub_category->id.',id',
                'category'=>'required',
                'status'=> 'required',
            ]);
            if($details){
    
                
                $sub_category->name=$request->name;
                $sub_category->slug=$request->slug;
                $sub_category->category_id=$request->category;
                $sub_category->status=$request->status;
                $sub_category->ShowHome=$request->ShowHome;
                $sub_category->save();
                
                $request->session()->flash('success','Sub-Category Updated successfully !');
                return response()->json([
                    'status'=> true,
                    'message' => 'Sub-Category Updated successfully !',
                ]);
    
            }else{
                return response()->json([
                    'status'=> false,
                    'errors' => $details,
                ]);
            }
        
        }

        
    public function destroy($ID,Request $request){
                $sub_category=SubCategory::find($ID);
                if(empty($sub_category)){
                    $request->session()->flash('error','Record not found');
                    return response()->json([
                        'status'=>true,
                        'notFound'=>'true',
                    ]);
                }
                $sub_category->delete();
                $request->session()->flash('success','Sub Category deleted successfully !');
                return response()->json([
                        'status'=>true,
                        'message'=>'Sub Category deleted successfully !',
                   ]);
            }
}
    
    

