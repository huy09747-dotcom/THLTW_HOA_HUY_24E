<?php

namespace Tests\Feature;

use App\Models\Donhang;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

class CompleteEcommerceTest extends ShopFeaturesTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::table('donhang', fn (Blueprint $t) => $t->integer('user_id')->nullable()->change());
        Schema::table('coupon_usages', fn (Blueprint $t) => $t->unsignedBigInteger('user_id')->nullable()->change());
        Schema::table('sanpham', function (Blueprint $t) {
            $t->string('description')->nullable();
            $t->string('sku')->nullable();
        });
        Schema::table('nguoidung', function (Blueprint $t) {
            $t->string('password')->nullable();
            $t->string('remember_token')->nullable();
        });
        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id();
            $t->morphs('tokenable');
            $t->string('name');
            $t->string('token', 64)->unique();
            $t->text('abilities')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
        Schema::create('guest_sessions', function (Blueprint $t) {
            $t->id();
            $t->string('token_hash', 64)->unique();
            $t->text('items')->nullable();
            $t->timestamp('expires_at');
            $t->timestamps();
        });
        Schema::create('stock_documents', function (Blueprint $t) {
            $t->id();
            $t->string('reference')->unique();
            $t->string('type');
            $t->string('reason');
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
        });
        Schema::create('chudebaiviet', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->integer('sort_order')->default(0);
            $t->string('description')->nullable();
            $t->integer('status');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('baiviet', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('topic_id')->nullable();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->text('detail');
            $t->string('post_type');
            $t->integer('status');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->unsignedBigInteger('updated_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('menu', function (Blueprint $t) {
            $t->id();
            $t->string('type');
            $t->unsignedBigInteger('table_id')->nullable();
            $t->softDeletes();
        });
        Schema::create('lienhe', function (Blueprint $t) {
            $t->id();
            foreach (['name', 'email', 'phone', 'title', 'content'] as $c) {
                $t->text($c);
            }$t->text('reply_content')->nullable();
            $t->integer('replay_id')->default(0);
            $t->timestamp('reply_at')->nullable();
            $t->integer('status');
            $t->timestamps();
            $t->softDeletes();
        });
    }

    private function admin(): void
    {
        Sanctum::actingAs(User::find(3));
    }

    public function test_stock_document_atomic_and_rejects_oversell_duplicates(): void
    {
        $this->postJson('/api/admin/stock', ['type' => 'in'])->assertForbidden();
        $this->admin();
        $body = ['type' => 'in', 'reason' => 'Nhập hàng', 'items' => [['product_id' => 1, 'qty' => 3, 'unit_cost' => 5000]]];
        $this->postJson('/api/admin/stock', $body)->assertCreated();
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 8]);
        $body['type'] = 'out';
        $body['items'][0]['qty'] = 9;
        $this->postJson('/api/admin/stock', $body)->assertUnprocessable();
        $this->assertDatabaseCount('stock_documents', 1);
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 8]);
        $body['items'][0]['qty'] = 2;
        $this->postJson('/api/admin/stock', $body)->assertCreated();
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 6]);
        $this->getJson('/api/admin/stock?type=out')->assertOk()->assertJsonPath('total', 1)->assertJsonPath('data.0.items.0.stock_before', 8)->assertJsonPath('data.0.items.0.stock_after', 6);
        $this->deleteJson('/api/products/1')->assertUnprocessable();
    }

    public function test_contact_topic_and_dynamic_page_crud_and_bindings(): void
    {
        $body = ['name' => 'Visitor', 'email' => 'visitor@example.com', 'phone' => '0901234567', 'title' => 'Support', 'content' => 'Please help'];
        $this->postJson('/api/contacts', $body)->assertCreated();
        $id = DB::table('lienhe')->value('id');
        $this->getJson('/api/admin/contacts')->assertForbidden();
        $this->admin();
        $this->deleteJson('/api/admin/contacts/'.$id)->assertUnprocessable();
        $this->putJson('/api/admin/contacts/'.$id, ['status' => 1, 'reply_content' => 'Handled'])->assertOk();
        $this->deleteJson('/api/admin/contacts/'.$id)->assertOk();
        $topic = ['name' => 'News', 'slug' => 'news-topic', 'status' => 1, 'sort_order' => 0];
        $id = $this->postJson('/api/admin/topics', $topic)->assertOk()->json('data.id');
        $page = ['title' => 'Warranty', 'slug' => 'warranty', 'detail' => 'Warranty details', 'status' => 1];
        $pageId = $this->postJson('/api/admin/pages', $page)->assertOk()->json('data.id');
        $this->getJson('/api/pages/warranty')->assertOk();
        $this->putJson('/api/admin/pages/'.$pageId, array_merge($page, ['status' => 0]))->assertOk();
        $this->getJson('/api/pages/warranty')->assertNotFound();
        $this->deleteJson('/api/admin/pages/'.$pageId)->assertOk();
        $this->deleteJson('/api/admin/topics/'.$id)->assertOk();
    }

    public function test_search_sort_hidden_detail_and_password_change(): void
    {
        DB::table('sanpham')->where('id', 1)->update(['description' => 'special description']);
        DB::table('sanpham')->insert(['id' => 2, 'name' => 'Cheap', 'slug' => 'cheap', 'status' => 1, 'price_sale' => 1000]);
        $this->getJson('/api/products?search=special')->assertOk()->assertJsonPath('data.0.id', 1);
        $this->getJson('/api/products?sort=price_asc')->assertOk()->assertJsonPath('data.0.id', 2);
        $this->getJson('/api/products?sort=price_desc')->assertOk()->assertJsonPath('data.0.id', 1);
        DB::table('sanpham')->where('id', 1)->update(['status' => 0]);
        $this->getJson('/api/products/phone')->assertNotFound();
        $u = User::find(1);
        $u->password = 'old-password';
        $u->save();
        $token = $u->createToken('current');
        $u->createToken('other');
        $this->actingAs($u->withAccessToken($token->accessToken), 'sanctum');
        $this->putJson('/api/profile/password', ['current_password' => 'wrong', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertUnprocessable();
        $this->putJson('/api/profile/password', ['current_password' => 'old-password', 'password' => 'new-password', 'password_confirmation' => 'new-password'])->assertOk();
        $this->assertTrue(Hash::check('new-password', $u->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_order_transition_history_filters_and_user_deletion(): void
    {
        $order = Donhang::create(['user_id' => 1, 'status' => 1, 'name' => 'Customer', 'email' => 'test@example.com', 'phone' => '0901234567', 'payment_method' => 'cod', 'payment_status' => 'unpaid', 'total' => 10000]);
        $this->admin();
        $this->patchJson('/api/donhangs/'.$order->id.'/status', ['status' => 4])->assertUnprocessable();
        foreach ([2, 3, 4] as $status) {
            $this->patchJson('/api/donhangs/'.$order->id.'/status', ['status' => $status])->assertOk();
        }
        $this->getJson('/api/donhangs/'.$order->id)->assertOk()->assertJsonCount(4, 'data.history');
        $this->getJson('/api/donhangs?search=HH-'.str_pad($order->id, 6, '0', STR_PAD_LEFT).'&status=4')->assertOk()->assertJsonPath('total', 1);
        $this->deleteJson('/api/admin/users/1')->assertUnprocessable();
        $this->deleteJson('/api/admin/users/2')->assertOk();
    }

    public function test_category_with_children_cannot_be_deleted(): void
    {
        Schema::table('danhmuc', function (Blueprint $t) {
            $t->string('name');
            $t->unsignedInteger('parent_id')->default(0);
        });
        DB::table('danhmuc')->insert([
            ['id' => 1, 'name' => 'Parent', 'parent_id' => 0],
            ['id' => 2, 'name' => 'Child', 'parent_id' => 1],
        ]);
        $this->admin();
        $this->deleteJson('/api/categories/1')->assertUnprocessable();
        $this->assertDatabaseHas('danhmuc', ['id' => 1, 'deleted_at' => null]);
    }

    public function test_guest_session_cart_coupon_checkout_and_access_isolation(): void
    {
        // Guest endpoints deliberately ignore any signed-in user; guest_session determines ownership.
        $guest = $this->postJson('/api/guest/session')->assertCreated()->json('token');
        $headers = ['X-Guest-Token' => $guest];
        $this->postJson('/api/guest/cart/items', ['product_id' => 1, 'quantity' => 2], $headers)->assertOk()->assertJsonPath('total', 20000);
        $this->putJson('/api/guest/cart/items/1', ['quantity' => 99], $headers)->assertUnprocessable();
        $this->coupon();
        $this->postJson('/api/guest/coupons/preview', ['coupon_code' => 'SAVE10'], $headers)->assertOk()->assertJsonPath('data.total', 18000);
        $result = $this->postJson('/api/guest/checkout', $this->shipping() + ['coupon_code' => 'SAVE10'], $headers)->assertCreated();
        $id = $result->json('data.id');
        $this->assertDatabaseHas('donhang', ['id' => $id, 'user_id' => null, 'total' => 18000]);
        $this->getJson('/api/guest/cart', $headers)->assertJsonCount(0, 'data');
        $this->getJson('/api/guest/orders/'.$id, $headers)->assertOk();
        $other = $this->postJson('/api/guest/session')->json('token');
        $this->getJson('/api/guest/orders/'.$id,['X-Guest-Token' => $other])->assertNotFound();
        $this->getJson('/api/guest/cart',['X-Guest-Token' => 'invalid'])->assertUnauthorized();
    }
}
