<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Database riêng trong RAM: không chạy migration hoặc ghi MySQL dự án.
        config(['database.default' => 'cart_test', 'database.connections.cart_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ], 'cache.default' => 'array']);
        DB::purge('cart_test');
        foreach ([
            'CREATE TABLE nguoidung (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, phone TEXT, username TEXT, password TEXT, roles TEXT, status INTEGER, created_at TEXT, updated_at TEXT, deleted_at TEXT)',
            'CREATE TABLE personal_access_tokens (id INTEGER PRIMARY KEY AUTOINCREMENT, tokenable_type TEXT, tokenable_id INTEGER, name TEXT, token TEXT UNIQUE, abilities TEXT, last_used_at TEXT, expires_at TEXT, created_at TEXT, updated_at TEXT)',
            'CREATE TABLE danhmuc (id INTEGER PRIMARY KEY, name TEXT, deleted_at TEXT)',
            'CREATE TABLE thuonghieu (id INTEGER PRIMARY KEY, name TEXT, deleted_at TEXT)',
            'CREATE TABLE sanpham (id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, brand_id INTEGER, name TEXT, slug TEXT, price_sale INTEGER, price_buy INTEGER, qty INTEGER, image TEXT, status INTEGER, is_available INTEGER, created_at TEXT, updated_at TEXT, deleted_at TEXT)',
            'CREATE TABLE giohang (id INTEGER PRIMARY KEY AUTOINCREMENT, nguoidung_id INTEGER REFERENCES nguoidung(id), created_at TEXT, updated_at TEXT)',
            'CREATE TABLE chitietgiohang (id INTEGER PRIMARY KEY AUTOINCREMENT, giohang_id INTEGER REFERENCES giohang(id), sanpham_id INTEGER REFERENCES sanpham(id), soluong INTEGER)',
        ] as $sql) {
            DB::statement($sql);
        }
        \Illuminate\Support\Facades\Schema::create('product_reviews', function (\Illuminate\Database\Schema\Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('product_id'); $t->integer('rating'); $t->boolean('is_visible')->default(true);
        });
        DB::table('sanpham')->insert(['id' => 1, 'name' => 'Test product', 'slug' => 'test-product', 'price_sale' => 200, 'price_buy' => 100, 'qty' => 5, 'status' => 1, 'is_available' => 1]);
    }

    private function token(): string
    {
        $user = new User(['name' => 'Test user', 'email' => uniqid().'@example.test', 'password' => 'Password123']);
        $user->roles = 'user';
        $user->status = 1;
        $user->save();

        return $user->createToken('test')->plainTextToken;
    }

    private function headers(string $token): array
    {
        app('auth')->forgetGuards();
        app('auth')->shouldUse('web');

        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_guest_cannot_access_cart_but_can_read_product(): void
    {
        $this->getJson('/api/cart')->assertUnauthorized();
        $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 1])->assertUnauthorized();
        $this->getJson('/api/products/test-product')->assertOk()->assertJsonPath('data.name', 'Test product');
        $this->getJson('/api/products/9999')->assertNotFound();
    }

    public function test_add_merge_update_and_delete(): void
    {
        $token = $this->token();
        $item = $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 2], $this->headers($token))
            ->assertCreated()->assertJsonPath('count', 2)->assertJsonPath('total', 400)->json('data.0.id');
        $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 1], $this->headers($token))
            ->assertCreated()->assertJsonCount(1, 'data')->assertJsonPath('count', 3);
        $this->putJson('/api/cart/items/'.$item, ['quantity' => 4], $this->headers($token))->assertOk()->assertJsonPath('total', 800);
        $this->deleteJson('/api/cart/items/'.$item, [], $this->headers($token))->assertOk()->assertJsonPath('count', 0);
    }

    public function test_cart_is_scoped_to_token_owner(): void
    {
        $owner = $this->token();
        $other = $this->token();
        $item = $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 1, 'nguoidung_id' => 2], $this->headers($owner))->assertCreated()->json('data.0.id');
        $this->getJson('/api/cart', $this->headers($other))->assertOk()->assertJsonPath('count', 0);
        $this->putJson('/api/cart/items/'.$item, ['quantity' => 2], $this->headers($other))->assertNotFound();
        $this->deleteJson('/api/cart/items/'.$item, [], $this->headers($other))->assertNotFound();
        $this->getJson('/api/giohangs', $this->headers($other))->assertForbidden();
        $this->getJson('/api/chitietgiohangs', $this->headers($other))->assertForbidden();
    }

    public function test_stock_validation_and_unavailable_product(): void
    {
        $token = $this->token();
        foreach ([0, -1, 1.5, 6, 1000] as $quantity) {
            $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => $quantity], $this->headers($token))->assertUnprocessable();
        }
        $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 4], $this->headers($token))->assertCreated();
        $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 2], $this->headers($token))->assertUnprocessable();
        DB::table('sanpham')->where('id', 1)->update(['status' => 2]);
        $this->getJson('/api/cart', $this->headers($token))->assertOk()->assertJsonPath('data.0.available', false)->assertJsonPath('total', 0);
        $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 1], $this->headers($token))->assertUnprocessable();
        DB::table('sanpham')->where('id', 1)->update(['status' => 1, 'price_sale' => null]);
        $this->postJson('/api/cart/items', ['product_id' => 1, 'quantity' => 1], $this->headers($token))->assertUnprocessable();
    }

    public function test_inactive_account_cannot_use_existing_token(): void
    {
        $token = $this->token();
        DB::table('nguoidung')->update(['status' => 2]);
        $this->getJson('/api/cart', $this->headers($token))->assertForbidden();
    }
}
