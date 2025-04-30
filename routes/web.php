<?php

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\PageController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/test', function () {
//     orderEmail(6);
// });

Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{sub_categorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');
Route::get('/checkout',[CartController::class,'checkout'])->name('front.checkout');
Route::post('/process-checkout',[CartController::class,'processCheckout'])->name('front.processCheckout');
Route::get('/thanks/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');
Route::post('/get-order-summery',[CartController::class,'getOrderSummery'])->name('front.getOrderSummery');
Route::post('/apply-discount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');
Route::post('/remove-discount',[CartController::class,'removeDiscount'])->name('front.removeDiscount');
Route::post('/add-to-wishlist',[FrontController::class,'addToWishlist'])->name('front.addToWishlist');
Route::get('/page/{slug}',[FrontController::class,'page'])->name('front.page');
Route::post('/contact-email',[FrontController::class,'sendContactEmail'])->name('front.sendContactEmail');

//forgot password

Route::get('/forgot-password',[AuthController::class,'forgotPassword'])->name('front.forgotPassword');
Route::post('/process-forgot-password',[AuthController::class,'processForgotPassword'])->name('front.processForgotPassword');
Route::post('/process-reset-password',[AuthController::class,'processResetPassword'])->name('front.processResetPassword');
Route::post('/save-rating/{id}',[ShopController::class,'saveRating'])->name('shop.saveRating');
Route::get('/reset-password/{token}',[AuthController::class,'resetPassword'])->name('front.resetPassword');



// Accounts Route


        
Route::group(['prefix' => 'accounts'], function(){
    Route::group(['middleware' => 'user.guest'],function(){
        Route::get('/register',[AuthController::class,'register'])->name('accounts.register');
        Route::post('/login',[AuthController::class,'authenticate'])->name('accounts.authenticate');

        Route::post('/process-register',[AuthController::class,'processRegister'])->name('accounts.processRegister');        
        Route::get('/login',[AuthController::class,'login'])->name('accounts.login');   

    });
    Route::group(['middleware' => 'user.auth'],function(){
        Route::get('/profile',[AuthController::class,'profile'])->name('accounts.profile');  
        Route::get('/change-password',[AuthController::class,'showChangePassword'])->name('accounts.changePassword');  
        Route::post('/process-change-password',[AuthController::class,'changePassword'])->name('accounts.processChangePassword');  

        Route::post('/update-profile',[AuthController::class,'updateProfile'])->name('accounts.updateProfile');  
        Route::post('/update-address',[AuthController::class,'updateAddress'])->name('accounts.updateAddress');  
        Route::get('/logout',[AuthController::class,'logout'])->name('accounts.logout');   
        Route::get('/my-orders',[AuthController::class,'orders'])->name('accounts.orders');
        Route::get('/wishlist',[AuthController::class,'wishlist'])->name('accounts.wishlist');
        Route::post('/remove-product-from-wishlist',[AuthController::class,'removeProductFromWishlist'])->name('accounts.removeProductFromWishlist');

        Route::get('/order-detail/{id}',[AuthController::class,'orderDetails'])->name('accounts.orders-detail');
           
    });

    
});





Route::group(['prefix' => 'admin'], function(){
    Route::group(['middleware' => 'admin.guest'],function(){
        Route::get('/login',[AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[AdminLoginController::class,'authenticate'])->name('admin.authenticate');
    });
    Route::group(['middleware' => 'admin.auth'],function(){
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');

        //setting route

        
        Route::get('/change-password',[SettingController::class,'showChangePasswordForm'])->name('admin.showChangePasswordForm');
        Route::post('/process-change-password',[SettingController::class,'changePassword'])->name('admin.processchangePassword');

        // orders Route

        Route::get('/orders',[OrderController::class,'index'])->name('orders.index');
        Route::get('/orders-details/{id}',[OrderController::class,'orderdetail'])->name('orders.order-details');
        Route::post('/order/change-status/{id}',[OrderController::class,'changeOrderStatus'])->name('orders.changeOrderStatus');
        Route::post('/order/send-email/{id}',[OrderController::class,'sendInvoiceEmail'])->name('orders.sendInvoiceEmail');

        // category route

        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');
        Route::post('/upload-temp-imgae',[TempImagesController::class,'create'])->name('temp-images.create');


        //Users Route
        Route::get('/users',[UserController::class,'index'])->name('users.index');
        Route::get('/users/create',[UserController::class,'create'])->name('users.create');
        Route::post('/users',[UserController::class,'store'])->name('users.store');
        Route::get('/users/{user}/edit',[UserController::class,'edit'])->name('users.edit');
        Route::put('/users/{user}',[UserController::class,'update'])->name('users.update');
        Route::delete('/users/{user}',[UserController::class,'destroy'])->name('users.delete');

        //pages route

        Route::get('/pages',[PageController::class,'index'])->name('page.index');
        Route::get('/pages/create',[PageController::class,'create'])->name('page.create');
        Route::post('/pages',[PageController::class,'store'])->name('page.store');
        Route::get('/pages/{page}/edit',[PageController::class,'edit'])->name('page.edit');
        Route::put('/pages/{page}',[PageController::class,'update'])->name('page.update');
        Route::delete('/pages/{page}',[PageController::class,'destroy'])->name('page.delete');


         // Sub category

         Route::get('/sub-categories/create',[SubCategoryController::class,'create'])->name('sub-categories.create');
         Route::post('/sub-categories',[SubCategoryController::class,'store'])->name('sub-categories.store');
         Route::get('/sub-categories',[SubCategoryController::class,'index'])->name('sub-categories.index');
         Route::get('/sub-categories/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
         Route::put('/sub-categories/{subCategory}',[SubCategoryController::class,'update'])->name('sub-categories.update');
         Route::delete('/sub-categories/{subCategory}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');

         // Brand route

         Route::get('/brands',[BrandsController::class,'index'])->name('brands.index');
         Route::get('/brands/create',[BrandsController::class,'create'])->name('brands.create');
         Route::post('/brands',[BrandsController::class,'store'])->name('brands.store');
         Route::put('/brands/{brands}',[BrandsController::class,'update'])->name('brands.update');
         Route::get('/brands/{brands}/edit',[BrandsController::class,'edit'])->name('brands.edit');
         Route::delete('/brands/{brands}',[BrandsController::class,'destroy'])->name('brands.delete');

         // products route
        Route::get('/products',[ProductController::class,'index'])->name('products.index');

        Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
        Route::post('/products',[ProductController::class,'store'])->name('products.store');
        Route::get('/products/{products}/edit',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{products}',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{products}',[ProductController::class,'destroy'])->name('products.delete');
        Route::get('/get-Products',[ProductController::class,'getProducts'])->name('products.getProducts');

        //product image route
        Route::post('/products-images/update',[ProductImageController::class,'update'])->name('products-images.update');
        Route::delete('/products-images',[ProductImageController::class,'destroy'])->name('products-images.destroy');
        Route::get('/product-subcategories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');
 
        // Shipping Route
         Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');
         Route::post('/shipping',[ShippingController::class,'store'])->name('shipping.store');
         Route::get('/shipping/{id}/edit',[ShippingController::class,'edit'])->name('shipping.edit');
         Route::put('/shipping/{id}',[ShippingController::class,'update'])->name('shipping.update');
         Route::delete('/shipping/{id}',[ShippingController::class,'destroy'])->name('shipping.delete');


         //Coupon Route

          Route::get('/coupons',[DiscountCodeController::class,'index'])->name('coupons.index');
          Route::get('/coupons/create',[DiscountCodeController::class,'create'])->name('coupons.create');
          Route::post('/coupons',[DiscountCodeController::class,'store'])->name('coupons.store');
          Route::get('/coupons/{coupons}/edit',[DiscountCodeController::class,'edit'])->name('coupons.edit');
          Route::put('/coupons/{coupons}',[DiscountCodeController::class,'update'])->name('coupons.update');
          Route::delete('/coupons/{coupons}',[DiscountCodeController::class,'destroy'])->name('coupons.delete');
        //  Route::get('/get-Products',[ProductController::class,'getProducts'])->name('products.getProducts');


        // slug generator

        Route::get('/getSlug',function(Request $request){
            $slug='';
            if (!empty($request->title)){
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug     
            ]);
        })->name('getSlug');
});
});
