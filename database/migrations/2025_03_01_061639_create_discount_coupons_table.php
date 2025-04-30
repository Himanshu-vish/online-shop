<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            
            //this discount coupon code 
            $table->string('code');
            //the human readable discount coupon code name
            $table->string('name')->nullable();
            //the discription of coupon - not neccessary
            $table->text('discription')->nullable();
            //the max uses this discount coupon has
            $table->integer('max_uses')->nullable();
            //hOW MANY TIMES A USER CAN USE THIS COUPON
            $table->integer('max_uses_user')->nullable();
            //whether or not the coupon is a percentage or a fixed price
            $table->enum('type',['percent','fixed'])->default('fixed');
            //the amount to discount based on type
            $table->double('discount_amount',10,2);
            //
            $table->double('min_amount',10,2)->nullable();
            //
            $table->integer('status')->default(1);
            //when the coupon begins
            $table->timestamp('starts_at')->nullable();
            //when the coupon ends
            $table->timestamp('expires_at')->nullable();
            //
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_coupons');
    }
}
