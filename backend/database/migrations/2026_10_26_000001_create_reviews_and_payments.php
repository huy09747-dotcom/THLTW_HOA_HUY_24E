<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('product_reviews',function(Blueprint $t){$t->id();$t->unsignedBigInteger('product_id')->index();$t->unsignedBigInteger('user_id');$t->unsignedTinyInteger('rating');$t->text('comment');$t->timestamps();$t->unique(['product_id','user_id']);});
  Schema::create('payment_attempts',function(Blueprint $t){$t->id();$t->unsignedBigInteger('order_id')->unique();$t->string('reference')->unique();$t->string('method');$t->unsignedBigInteger('amount');$t->string('currency',3)->default('VND');$t->string('gateway_id')->nullable();$t->text('url')->nullable();$t->string('status')->default('pending');$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('payment_attempts');Schema::dropIfExists('product_reviews');}
};
