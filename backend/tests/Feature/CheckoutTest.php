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
    use \Tests\CompletionSchema;
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Mail::fake();
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
        $this->completionSchema();
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

    public function test_order_detail_is_private_and_preserves_purchase_snapshot(): void
    {
        $this->getJson('/api/my-orders/1')->assertUnauthorized();
        Sanctum::actingAs(User::findOrFail(1));
        $id = $this->postJson('/api/checkout', $this->shipping())->assertCreated()->json('data.id');
        DB::table('sanpham')->where('id', 1)->update(['name'=>'Changed', 'price_sale'=>999, 'deleted_at'=>now()]);
        $this->getJson('/api/my-orders/'.$id)->assertOk()
            ->assertJsonPath('data.items.0.item_name','Phone')
            ->assertJsonPath('data.items.0.price',100)
            ->assertJsonPath('data.items.0.qty',2)
            ->assertJsonPath('data.total',200)
            ->assertJsonPath('data.address','123 Test')
            ->assertJsonMissingPath('data.user_id');
        Sanctum::actingAs(User::findOrFail(2));
        $this->getJson('/api/my-orders/'.$id)->assertNotFound();
        $this->getJson('/api/my-orders/9999')->assertNotFound();
        Sanctum::actingAs(User::findOrFail(1));
        DB::table('donhang')->where('id',$id)->update(['deleted_at'=>now()]);
        $this->getJson('/api/my-orders/'.$id)->assertNotFound();
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

    public function test_discount_is_used_when_creating_order(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0);
        });
        DB::table('sanpham')->where('id', 1)->update(['discount_percent' => 10]);
        Sanctum::actingAs(User::findOrFail(1));
        $this->postJson('/api/checkout', $this->shipping())
            ->assertCreated()->assertJsonPath('data.total', 180);
        $this->assertDatabaseHas('chitietdonhang', ['price' => 90, 'qty' => 2, 'amount' => 180]);
    }

    public function test_delete_is_blocked_for_categories_and_brands_with_trashed_products(): void
    {
        foreach (['danhmuc', 'thuonghieu'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->softDeletes();
            });
            DB::table($name)->insert(['id' => 1]);
        }
        Schema::table('sanpham', function (Blueprint $table) {
            $table->integer('category_id')->nullable();
            $table->integer('brand_id')->nullable();
        });
        DB::table('sanpham')->where('id', 1)->update([
            'category_id' => 1, 'brand_id' => 1, 'deleted_at' => now(),
        ]);
        foreach ([
            [new \App\Http\Controllers\Api\CategoryController, 'danhmuc'],
            [new \App\Http\Controllers\Api\BrandController, 'thuonghieu'],
        ] as [$controller, $table]) {
            foreach (['destroy', 'forceDelete'] as $method) {
                if ($method === 'forceDelete') {
                    DB::table($table)->where('id', 1)->update(['deleted_at' => now()]);
                }
                try {
                    $controller->$method(1);
                    $this->fail('Deletion should be blocked');
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->assertArrayHasKey('products', $e->errors());
                }
                $this->assertDatabaseCount($table, 1);
            }
        }
    }

    public function test_sale_and_discount_cannot_go_below_cost(): void
    {
        $request = \App\Http\Requests\ProductRequest::create('/', 'POST', [
            'price_buy' => 90, 'price_sale' => 100, 'discount_percent' => 20,
        ]);
        $validator = \Illuminate\Support\Facades\Validator::make([], []);
        ($request->after()[0])($validator);
        $this->assertTrue($validator->errors()->has('discount_percent'));
        $validator = \Illuminate\Support\Facades\Validator::make(
            ['price_buy' => 100, 'price_sale' => 90],
            ['price_buy' => $request->rules()['price_buy'], 'price_sale' => $request->rules()['price_sale']]
        );
        $this->assertTrue($validator->fails());
    }
}
