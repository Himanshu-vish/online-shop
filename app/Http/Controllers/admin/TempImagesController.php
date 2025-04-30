<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
//use Illuminate\Support\Facades\File;

class TempImagesController extends Controller
{
   public function create(Request $request){

     $image = $request->image;
     if(!empty($image)){
        $ext=$image->getClientOriginalExtension();
        $newName=time().'.'.$ext;
        $tempImage=new TempImage();
        $tempImage->name=$newName;
        $tempImage->save();
        $image->move(public_path().'/temp',$newName);

        // generate thumbnail

        $sourcePath=public_path().'/temp/'.$newName;
        $destinationPath=public_path().'/temp/thumb/'.$newName;
        $image=Image::make($sourcePath);
        $image->resize(300, 275, function ($constraint) {
        $constraint->aspectRatio();
        });
       $image->save($destinationPath);
        return response()->json([
            'status'=>true,
            'image_id'=>$tempImage->id,
            'ImagePath'=>asset('/temp/thumb/'.$newName),
            'message'=>'image uploaded successfully'
        ]);

     }
   }
}
