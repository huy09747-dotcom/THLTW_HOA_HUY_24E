<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $t) {
            $t->id(); $t->string('code', 40)->unique();
            $t->string('type', 10); $t->unsignedBigInteger('value');
            $t->unsignedBigInteger('min_order')->default(0);
            $t->unsignedBigInteger('max_discount')->nullable();
            $t->unsignedInteger('usage_limit')->nullable();
            $t->dateTime('starts_at')->nullable(); $t->dateTime('ends_at')->nullable();
            $t->boolean('active')->default(true); $t->timestamps();
        });
        Schema::create('coupon_usages', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('coupon_id')->index();
            $t->unsignedBigInteger('user_id')->index();
            $t->unsignedBigInteger('order_id')->unique();
            $t->unsignedBigInteger('discount'); $t->timestamps();
        });
        Schema::create('favorites', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id');
            $t->unsignedBigInteger('product_id'); $t->timestamps();
            $t->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
