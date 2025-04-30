<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\Category::factory(30)->create();
         \App\Models\Product::factory(10)->create();
       //  \App\Models\SubCategory::factory(10)->create();
    }
}
