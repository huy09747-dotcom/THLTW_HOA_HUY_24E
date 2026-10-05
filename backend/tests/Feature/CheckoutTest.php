<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('sqlite', config('database.default'));
        foreach ([
            'nguoidung' => ['status'],
            'giohang' => ['nguoidung_id'],
            'chitietgiohang' => ['giohang_id', 'sanpham_id', 'soluong'],
            'sanpham' => ['status', 'is_available', 'price_sale', 'qty'],
            'donhang' => ['user_id', 'status', 'subtotal', 'discount', 'total'],
            'chitietdonhang' => ['order_id', 'product_id', 'price', 'qty', 'amount'],
        ] as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($name, $columns) {
                $table->id();
                foreach ($columns as $column) {
                    $table->integer($column);
                }
                $strings = match ($name) {
                    'sanpham' => ['name'],
                    'donhang' => ['name', 'phone', 'email', 'address', 'note', 'code', 'order_type', 'payment_method', 'payment_status'],
                    'chitietdonhang' => ['item_name'],
                    default => [],
                };
                foreach ($strings as $column) {
                    $table->string($column)->nullable();
                }
                $table->timestamps();
                $table->softDeletes();
            });
        }
        DB::table('nguoidung')->insert([['id' => 1, 'status' => 1], ['id' => 2, 'status' => 1]]);
        DB::table('giohang')->insert([['id' => 1, 'nguoidung_id' => 1], ['id' => 2, 'nguoidung_id' => 2]]);
        DB::table('sanpham')->insert(['id' => 1, 'name' => 'Phone', 'status' => 1, 'is_available' => 1, 'price_sale' => 100, 'qty' => 5]);
        DB::table('chitietgiohang')->insert([
            ['giohang_id' => 1, 'sanpham_id' => 1, 'soluong' => 2],
            ['giohang_id' => 2, 'sanpham_id' => 1, 'soluong' => 1],
        ]);
    }

    private function shipping(): array
    {
        return ['name' => 'Test', 'phone' => '0901234567', 'email' => 'test@example.com', 'address' => '123 Test'];
    }

    public function test_checkout_calculates_total_and_only_clears_own_cart(): void
    {
        Sanctum::actingAs(User::findOrFail(1));
        $this->postJson('/api/checkout', $this->shipping() + ['total' => 1, 'user_id' => 2])
            ->assertCreated()->assertJsonPath('data.total', 200);
        $this->assertDatabaseHas('donhang', ['user_id' => 1, 'total' => 200, 'payment_status' => 'unpaid']);
        $this->assertDatabaseHas('chitietdonhang', ['qty' => 2, 'price' => 100, 'amount' => 200]);
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 3]);
        $this->assertDatabaseMissing('chitietgiohang', ['giohang_id' => 1]);
        $this->assertDatabaseHas('chitietgiohang', ['giohang_id' => 2]);
        $this->postJson('/api/checkout', $this->shipping())->assertUnprocessable();
        $this->assertDatabaseCount('donhang', 1);
    }

    public function test_insufficient_stock_leaves_cart_and_orders_unchanged(): void
    {
        Sanctum::actingAs(User::findOrFail(1));
        DB::table('sanpham')->where('id', 1)->update(['qty' => 1]);
        $this->postJson('/api/checkout', $this->shipping())->assertUnprocessable();
        $this->assertDatabaseCount('donhang', 0);
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 1]);
        $this->assertDatabaseHas('chitietgiohang', ['giohang_id' => 1]);
    }

    public function test_authentication_and_shipping_validation(): void
    {
        $this->postJson('/api/checkout', $this->shipping())->assertUnauthorized();
        Sanctum::actingAs(User::findOrFail(1));
        $this->postJson('/api/checkout', [])->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone', 'email', 'address']);
        $this->assertDatabaseCount('donhang', 0);
    }
}
