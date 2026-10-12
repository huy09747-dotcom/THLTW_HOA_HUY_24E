<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));

        Schema::create('nguoidung', function (Blueprint $table) {
            $table->id();
            foreach (['name', 'username', 'email', 'phone', 'address', 'password', 'remember_token', 'roles'] as $column) {
                $table->string($column)->nullable();
            }
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('donhang', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('code');
            $table->integer('total')->default(10000);
            $table->integer('status')->default(1);
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_method')->default('cod');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('nguoidung')->insert([
            ['id' => 1, 'name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password123'), 'roles' => 'admin', 'status' => 1],
            ['id' => 2, 'name' => 'Customer', 'email' => 'customer@example.com', 'password' => Hash::make('password123'), 'roles' => 'user', 'status' => 1],
        ]);
    }

    public function test_only_active_admin_can_access_user_management(): void
    {
        $this->getJson('/api/admin/users')->assertUnauthorized();
        Sanctum::actingAs(User::findOrFail(2));
        $this->getJson('/api/admin/users')->assertForbidden();
        $this->getJson('/api/admin/users/2')->assertForbidden();
        $this->getJson('/api/admin/users/2/orders')->assertForbidden();
        $this->patchJson('/api/admin/users/2/status', ['status' => 0])->assertForbidden();
        $admin = User::findOrFail(1);
        $admin->status = 0;
        Sanctum::actingAs($admin);
        $this->getJson('/api/admin/users')->assertForbidden();
    }

    public function test_customer_list_is_paginated_newest_first_without_secrets(): void
    {
        for ($id = 3; $id <= 13; $id++) {
            DB::table('nguoidung')->insert(['id' => $id, 'name' => "User $id", 'roles' => 'user']);
        }
        Sanctum::actingAs(User::findOrFail(1));
        $this->getJson('/api/admin/users')
            ->assertOk()->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.id', 13)
            ->assertJsonPath('meta.total', 12)
            ->assertJsonMissingPath('data.0.password')
            ->assertJsonMissingPath('data.0.remember_token');
        $this->getJson('/api/admin/users?page=2')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/admin/users?page=0')->assertUnprocessable();
        $this->getJson('/api/admin/users/1')->assertNotFound();
        $this->getJson('/api/admin/users/2')->assertOk()->assertJsonPath('data.email', 'customer@example.com');
    }

    public function test_block_revokes_tokens_and_unblock_preserves_account_and_orders(): void
    {
        $customer = User::findOrFail(2);
        $customer->createToken('first');
        $customer->createToken('second');
        DB::table('donhang')->insert(['user_id' => 2, 'code' => 'KEEP']);
        Sanctum::actingAs(User::findOrFail(1));
        $this->patchJson('/api/admin/users/2/status', ['status' => 0])
            ->assertOk()->assertJsonPath('data.status', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseHas('nguoidung', ['id' => 2, 'status' => 0]);
        $this->postJson('/api/login', ['email' => 'customer@example.com', 'password' => 'password123'])->assertForbidden();
        $this->patchJson('/api/admin/users/2/status', ['status' => 1])->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseHas('donhang', ['code' => 'KEEP']);
        $this->postJson('/api/login', ['email' => 'customer@example.com', 'password' => 'password123'])
            ->assertOk()->assertJsonStructure(['token']);
        $this->patchJson('/api/admin/users/1/status', ['status' => 0])->assertNotFound();
        $this->patchJson('/api/admin/users/2/status', ['status' => 9])->assertUnprocessable();
    }

    public function test_orders_belong_only_to_selected_customer_and_have_ten_per_page(): void
    {
        for ($id = 1; $id <= 11; $id++) {
            DB::table('donhang')->insert(['user_id' => 2, 'code' => "ORDER-$id"]);
        }
        DB::table('donhang')->insert(['user_id' => 1, 'code' => 'OTHER']);
        Sanctum::actingAs(User::findOrFail(1));
        $this->getJson('/api/admin/users/2/orders')
            ->assertOk()->assertJsonCount(10, 'data')
            ->assertJsonPath('total', 11)
            ->assertJsonPath('data.0.code', 'ORDER-11')
            ->assertJsonMissing(['code' => 'OTHER']);
        $this->getJson('/api/admin/users/2/orders?page=2')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/admin/users/999/orders')->assertNotFound();
    }
}
