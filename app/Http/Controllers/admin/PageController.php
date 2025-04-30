<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request){
        $pages=Page::latest();
        if (!empty($request->get( 'keyword'))){
            $pages->where('name','like','%'.$request->get( 'keyword').'%');
        }
        $pages=$pages->paginate(10);
        $data['pages']=$pages;
        return view('admin.pages.list',$data);

    }

    public function create(){
        return view('admin.pages.create');
        
    }

    public function store(Request $request){
        $details=$request->validate([
            'name' => 'required',
            'slug' => 'required',
        ]);
        if ($details){

            $page=new Page();
            $page->name = $request->name;
            $page->slug = $request->slug;
            $page->content = $request->content;
            $page->save();
            session()->flash('success','Page Create Successfully!');
            return response()->json([
               'status' => true,
               'message' => 'Page Create Successfully!'
            ]);

        }else{
            return response()->json([
               'status' => false,
               'errors' => $details,
            ]);
        }

        
    }

    public function edit(Request $request,$pageID){
        $page=Page::find($pageID);
        if(empty($page)){
            redirect()->route('page.index');
        }
        $data['page']=$page;
        return view('admin.pages.edit',$data);
        
    }

    public function update(Request $request,$pageID){
        $page=Page::find($pageID);
        if(empty($page)){
            $request->session()->flash('error','record not found');
            return response()->json([
                'status'=>false,
                'notFound'=>true,
                'message'=>'record not found',
            ]);
        }
        $details=$request->validate([
            'name' => 'required',
            'slug' => 'required|unique:pages,slug,'.$page->id.',id',
        ]);
        if ($details){ 
            $page->name=$request->name;
            $page->slug=$request->slug;
            $page->content=$request->content;
            $page->save();
        
           
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

    public function destroy($pageID,Request $request){
        $page=Page::find($pageID);
        if(empty($page)){
            $request->session()->flash('error','page not found');
            return response()->json([
                'status'=>true,
                'message'=>'page not found',
            ]);
        }
            
        $page->delete();
        $request->session()->flash('success','Page deleted successfully !');
            return response()->json([
                'status'=>true,
                'message'=>'Page deleted successfully !',
            ]);
    }
        
}

