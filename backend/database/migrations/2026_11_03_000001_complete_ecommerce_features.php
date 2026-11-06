<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donhang', function (Blueprint $t) {
            $t->unsignedInteger('user_id')->nullable()->change();
            $t->unsignedBigInteger('guest_session_id')->nullable()->index();
        });
        Schema::table('coupon_usages', fn (Blueprint $t) => $t->unsignedBigInteger('user_id')->nullable()->change());
        Schema::create('guest_sessions', function (Blueprint $t) {
            $t->id();
            $t->string('token_hash', 64)->unique();
            $t->json('items')->nullable();
            $t->timestamp('expires_at');
            $t->timestamps();
        });
        Schema::create('order_status_history', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id')->index();
            $t->unsignedTinyInteger('from_status')->nullable();
            $t->unsignedTinyInteger('to_status');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->timestamp('created_at');
        });
        Schema::create('stock_documents', function (Blueprint $t) {
            $t->id();
            $t->string('type', 3);
            $t->string('reference')->unique();
            $t->string('reason', 1000);
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
        });
        Schema::create('stock_document_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('document_id')->index();
            $t->unsignedBigInteger('product_id')->index();
            $t->string('product_name');
            $t->unsignedInteger('qty');
            $t->unsignedBigInteger('unit_cost')->default(0);
            $t->unsignedInteger('stock_before');
            $t->unsignedInteger('stock_after');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_document_items');
        Schema::dropIfExists('stock_documents');
        Schema::dropIfExists('order_status_history');
        Schema::table('donhang', fn (Blueprint $t) => $t->dropColumn('guest_session_id'));
        Schema::dropIfExists('guest_sessions');
        // Keep user_id nullable: existing guest orders must not be lost on rollback.
    }
};
