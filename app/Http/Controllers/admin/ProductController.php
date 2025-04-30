<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;


class ProductController extends Controller
{

    public function index(Request $request){
        $products=Product::latest('id')->with('product_images');
        if($request->get('keyword')){
            $products->where('title','like','%'.$request->keyword.'%');
           }
        $products=$products->paginate(10);   
        $data['products']=$products;
        return view('admin.products.list',$data,compact('products'));
    }

    
    public function create(){
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $brands=Brands::orderBy('name','ASC')->get();
        $data['brands']=$brands;
        return view('admin.products.create',$data);
    }

    public function store(Request $request){

        // dd($request->image_array);
        // exit();

        $rules=[
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'brand' => 'required|numeric',
            'is_feature' => 'required|in:Yes,No',
            
        ];

        if(!empty($request->track_qty) && $request->track_qty=='Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $details=$request->validate($rules);
    
        if($details){

            $product = new Product();
            $product->title=$request->title;        
            $product->slug=$request->slug;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->compare_price=$request->compare_price;
            $product->category_id=$request->category;
            $product->sub_category_id=$request->sub_category;
            $product->brand_id=$request->brand;
            $product->is_feature=$request->is_feature;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->track_qty=$request->track_qty;
            $product->qty=$request->qty;
            $product->status=$request->status;
            $product->shipping_returns=$request->shipping_returns;
            $product->short_description=$request->short_description;
            $product->related_products=(!empty($request->related_product)) ? implode(',',$request->related_product): '';

            $product->save();

            // save gallery

            if(!empty($request->image_array)){
              foreach($request->image_array as $temp_image_id) {
                $tempImageInfo=TempImage::find($temp_image_id);
                $extArray=explode('.',$tempImageInfo->name);
                $ext=last($extArray); // like .jpg .gif .jpeg .png

                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = 'NULL';
                $productImage->save();

                $imageName=$product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                $productImage->image=$imageName;
                $productImage->save();


                //generate product thumbnail
                

                //large
                $sourcePath=public_path().'/temp/'.$tempImageInfo->name;
                $destinationPath=public_path().'/uploads/product/large/'. $imageName;
                $image=Image::make($sourcePath);
                $image->resize(1400,null,function($constraint){
                    $constraint->aspectRatio();
                });
                $image->save($destinationPath);

                // small

                
                $destinationPath=public_path().'/uploads/product/small/'. $imageName;
                $image=Image::make($sourcePath);
                $image->resize(300,300,function($constraint){
                    $constraint->aspectRatio();
                });
                $image->save($destinationPath);





              }
            }


            $request->session()->flash('success','Product Added successfully !');
            return response()->json([
                'status'=> true,
                'message' => 'Product Added successfully !',
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'errors' => $details,
            ]);
        }

    }

    public function edit($productID,Request $request){
        $product=Product::find($productID);

        if(empty($product)){
            redirect()->route('categories.index')->with('error','Record Not Found');
         }
        $productImage=ProductImage::where('product_id',$product->id)->get();
        $sub_categories=SubCategory::where('category_id',$product->category_id)->get();
        $related_Products=[];
        // fetch related product

        if($product->related_products != ''){ //field name ->related_products
            $productArray=explode(',' , $product->related_products);
            $related_Products=Product::whereIn('id', $productArray)->get();
        }
        
        $categories=Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['sub_categories']=$sub_categories;
        $data['productImage']=$productImage;
        $data['related_Products']=$related_Products;
        
        $brands=Brands::orderBy('name','ASC')->get();
        $data['brands']=$brands;
        return view('admin.products.edit',$data,compact('product'));
     

    }

    public function update($id,Request $request){
        $product=Product::find($id);
        $rules=[
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'brand' => 'required|numeric',
            'is_feature' => 'required|in:Yes,No',
            
        ];

        if(!empty($request->track_qty) && $request->track_qty=='Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $details=$request->validate($rules);
    
        if($details){

            
            $product->title=$request->title;        
            $product->slug=$request->slug;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->compare_price=$request->compare_price;
            $product->category_id=$request->category;
            $product->sub_category_id=$request->sub_category;
            $product->brand_id=$request->brand;
            $product->is_feature=$request->is_feature;
            $product->sku=$request->sku;
            $product->barcode=$request->barcode;
            $product->track_qty=$request->track_qty;
            $product->qty=$request->qty;
            $product->status=$request->status;
            $product->shipping_returns=$request->shipping_returns;
            $product->short_description=$request->short_description;
            $product->related_products=(!empty($request->related_product)) ? implode(',',$request->related_product): '';
            $product->save();

            // save gallery



            $request->session()->flash('success','Product Updated successfully !');
            return response()->json([
                'status'=> true,
                'message' => 'Product Updated successfully !',
            ]);

        }else{
            return response()->json([
                'status'=> false,
                'errors' => $details,
            ]);
        }


    }

    public function destroy($id,Request $request){
        $product=Product::find($id);
        if(empty($product)){
            $request->session()->flash('error','product not found');
            return response()->json([
                'status'=>false,
                'message'=>'product not found',
            ]);
        }
        $productImages=ProductImage::where('product_id',$id)->get();
        if(!empty($productImages)){
            foreach ($productImages as $productImage) {
                file::delete(public_path('uploads/product/large/'.$productImage->image));
                file::delete(public_path('uploads/product/small/'.$productImage->image));
            }
            $productImages=ProductImage::where('product_id',$id)->delete();
        }
        $product->delete();
        $request->session()->flash('success','product deleted successfully !');
        return response()->json([
            'status'=>true,
            'message'=>'product deleted successfully !',
        ]);
       } 

       //Related Product admin side---->
       public function getProducts(Request $request) {
        $tempProduct=[];
        if($request->term != ""){
          $products=Product::where('title','like','%'.$request->term.'%')->get();
          if($products != null){
            foreach ($products as $product) {
                $tempProduct[]=array('id'=>$product->id,'text'=>$product->title);
            }
          }
        }
        // print_r($tempProduct);
        return response()->json([
            'tags'=>$tempProduct,
            'status'=>true
        ]);
       }
    
}
