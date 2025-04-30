<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title=$this->faker->unique()->name();
        $slug=Str::slug($title);

        $sub_categories=[1,2];
        $subCatRandKey=array_rand($sub_categories);
        $brands=[1,2];
        $brandCatRandKey=array_rand($brands);

        return [
         'title'=>$title,
         'slug'=>$slug,
         'category_id'=>1,
         'sub_category_id' => $sub_categories[$subCatRandKey],
         'brand_id'=>$brands[$brandCatRandKey],
         'price'=>rand(10,100000),
         'sku'=>rand(15,1000),
         'track_qty'=>'Yes',
         'qty'=>10,
         'is_feature'=>'Yes',
         'status'=>1,
        ];
    }
} 
