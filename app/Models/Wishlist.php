<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = 'wishlist'; // 👈 Define Table Name
    protected $fillable = ['user_id', 'product_id'];


    // this method use for print wishlist item 'wishlist blade'
    public function product(){
       return $this->belongsTo(Product::class);
    }
}
