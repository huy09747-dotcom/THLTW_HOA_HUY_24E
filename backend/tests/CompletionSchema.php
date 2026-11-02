<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CompletionSchema
{
    protected function completionSchema(): void
    {
        if (! Schema::hasColumn('donhang', 'guest_session_id')) {
            Schema::table('donhang', fn (Blueprint $t) => $t->unsignedBigInteger('guest_session_id')->nullable());
        }
        Schema::create('order_status_history', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('order_id');
            $t->integer('from_status')->nullable();
            $t->integer('to_status');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->timestamp('created_at');
        });
        Schema::create('stock_document_items', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('document_id');
            $t->unsignedBigInteger('product_id');
            $t->string('product_name');
            $t->integer('qty');
            $t->integer('unit_cost');
            $t->integer('stock_before');
            $t->integer('stock_after');
        });
    }
}
