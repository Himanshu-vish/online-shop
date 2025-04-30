<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request,$categorySlug=null,$sub_catgeorySlug=null){

        $categorySelected='';
        $sub_categorySelected='';
        $brandsArray=[];
        //dd($request->get('brand'));

      
      //  dd($brandsArray);

       // $products=Product::orderBy('id','DESC')->where('status','1')->get();
        $categories=Category::orderBy('name','ASC')->where('status','1')->get();
        $brands=Brands::orderBy('name','ASC')->where('status','1')->get();

        $products=Product::where('status','1');

        //apply filter here  

        if(!empty($categorySlug)){
            $category=Category::where('slug',$categorySlug)->first();
            $products=$products->where('category_id',$category->id);
            $categorySelected=$category->id;
        }

        
        if(!empty($sub_catgeorySlug)){
            $sub_category=SubCategory::where('slug',$sub_catgeorySlug)->first();
            $products=$products->where('sub_category_id',$sub_category->id);
            //dd($sub_category->id);
            $sub_categorySelected=$sub_category->id;
        }

        if(!empty($request->get('brand'))){
            $brandsArray = $request->get('brand',[]);
            $products=$products->whereIn('brand_id',$brandsArray);
        }

        if($request->get('price_max') != '' && $request->get('price_min') != ''){
            if($request->get('price_max') == 1000){
                $products=$products->whereBetween('price',[intval($request->get('price_min')),100000]);  
            }else{
                $products=$products->whereBetween('price',[intval($request->get('price_min')),intval($request->get('price_max'))]);  
            }
            
        }

        if(!empty($request->get('search'))){
            $products=$products->where('title','like','%'.$request->get('search').'%');
        }
        
        
        if($request->get('sort') != ''){
           if($request->get('sort') == 'latest'){
            $products=$products->orderBy('id','DESC');     
           }elseif($request->get('sort') == 'price_asc'){
            $products=$products->orderBy('price','ASC');     
           }else{
            $products=$products->orderBy('price','DESC');     
           }
        }else{

        }

        $products=$products->paginate(6);
       
        $data['products']=$products;
        $data['categories']=$categories;
        $data['brands']=$brands;
        $data['categorySelected']=$categorySelected;
        $data['sub_categorySelected']=$sub_categorySelected; 
        $data['brandsArray']=$brandsArray;
        $data['priceMax'] = intval($request->get('price_max'));
        $data['priceMin']=intval($request->get('price_min'));
        $data['sort']=$request->get('sort');
        return view('front.shop',$data);
    }

    public function product($slug){
        //echo $slug;
        $product=Product::where('slug',$slug)
                         ->withCount('product_rating')
                         ->withSum('product_rating','rating')
                         ->with(['product_images','product_rating'])
                         ->first();
        if($product == null){
            abort(404);
        }

        $related_Products=[];
        // fetch related product

        if($product->related_products != ''){ //field name ->related_products
            $productArray=explode(',' , $product->related_products);
            $related_Products=Product::whereIn('id', $productArray)->with('product_images')->get();
        }

        $avgRating='0.0';
        $avgRatingPer=0;
        if($product->product_rating_count > 0){
            $avgRating=number_format(($product->product_rating_sum_rating/$product->product_rating_count),2);
            $avgRatingPer=($avgRating*100)/5;
        }
        $data['product']=$product;
        $data['related_Products']=$related_Products;
        $data['avgRating']=$avgRating;
        $data['avgRatingPer']=$avgRating;

        return view('front.product',$data);
    }

    public function saveRating($id,Request $request){
        $details=$request->validate([
           'username' => 'required',
           'email' => 'required|email',
           'comment' => 'required',
           'rating' => 'required'
        ]);

        if($details){

            $count=ProductRating::where('email',$request->email)->count();
            if($count > 0){
              session()->flash('error','You Already Rated!');
              return response()->json([
                 'status' => true,
              ]);
            }

            $productRating=new ProductRating();
            $productRating->product_id=$id;
            $productRating->username=$request->username;
            $productRating->email=$request->email;
            $productRating->rating=$request->rating;
            $productRating->comment=$request->comment;
            $productRating->status=0;
            $productRating->save();
            session()->flash('success','Thanks For Your Rating');
            return response()->json([
                'status' => true,
                'message' => 'Thanks For Your Rating',
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $details,
            ]);
        }
    }
}
