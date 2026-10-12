<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ShopCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        foreach (['danhmuc', 'thuonghieu'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->softDeletes();
            });
            DB::table($name)->insert(['id' => 1, 'name' => 'Test']);
        }
        Schema::create('sanpham', function (Blueprint $table) {
            $table->id();
            foreach (['category_id', 'brand_id', 'price_sale', 'discount_percent', 'is_featured', 'status'] as $column) {
                $table->integer($column)->nullable();
            }
            $table->string('name');
            $table->string('slug');
            $table->string('sku')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        for ($id = 1; $id <= 12; $id++) {
            DB::table('sanpham')->insert([
                'id' => $id, 'name' => "Phone $id", 'slug' => "phone-$id",
                'category_id' => 1, 'brand_id' => 1, 'price_sale' => 100000,
                'discount_percent' => $id === 1 ? 20 : 0,
                'is_featured' => $id === 2 ? 1 : 0, 'status' => $id === 12 ? 0 : 1,
            ]);
        }
    }

    public function test_catalog_paginates_and_excludes_hidden_with_shop_status(): void
    {
        $this->getJson('/api/products?status=1')->assertOk()
            ->assertJsonCount(10, 'data')->assertJsonPath('meta.total', 11)
            ->assertJsonPath('data.0.id', 11);
        $this->getJson('/api/products?status=1&page=2')->assertJsonCount(1, 'data');
    }

    public function test_hot_sale_and_combined_filters_use_final_price(): void
    {
        $this->getJson('/api/products?status=1&is_featured=1')->assertOk()->assertJsonPath('data.0.id', 2)->assertJsonCount(1, 'data');
        $this->getJson('/api/products?status=1&on_sale=1')->assertOk()->assertJsonPath('data.0.id', 1)->assertJsonCount(1, 'data');
        $this->getJson('/api/products?status=1&category_id=1&brand_id=1&search=Phone&min_price=80000&max_price=80000')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.final_price', 80000);
        $this->getJson('/api/products?status=1&brand_id=999')->assertJsonCount(0, 'data');
        $this->getJson('/api/products?min_price=100000&max_price=80000')->assertUnprocessable();
        $this->getJson('/api/products?search[]=bad')->assertUnprocessable();
    }
}
