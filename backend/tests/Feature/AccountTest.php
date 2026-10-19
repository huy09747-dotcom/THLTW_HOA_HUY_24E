<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountTest extends TestCase
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


    public function test_registration_cannot_choose_admin_role(): void
    {
        $this->postJson('/api/register', ['name'=>'New', 'username'=>'new', 'email'=>'new@example.com', 'phone'=>'0901234567', 'password'=>'password123', 'password_confirmation'=>'password123', 'roles'=>'admin', 'status'=>0])
            ->assertCreated()->assertJsonPath('user.roles', 'user')->assertJsonPath('user.status', 1)->assertJsonMissingPath('user.password');
    }

    public function test_login_checks_audience_before_issuing_token(): void
    {
        $this->postJson('/api/login', ['email'=>'customer@example.com', 'password'=>'password123', 'audience'=>'admin'])->assertForbidden();
        $this->postJson('/api/login', ['email'=>'admin@example.com', 'password'=>'password123', 'audience'=>'user'])->assertForbidden();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->postJson('/api/login', ['email'=>'admin@example.com', 'password'=>'password123', 'audience'=>'admin'])->assertOk();
        $this->postJson('/api/login', ['email'=>'customer@example.com', 'password'=>'password123', 'audience'=>'user'])->assertOk();
    }

    public function test_profile_only_updates_current_user_and_allowed_fields(): void
    {
        $this->putJson('/api/profile', [])->assertUnauthorized();
        Sanctum::actingAs(User::findOrFail(2));
        $body=['name'=>'Updated', 'username'=>'customer', 'email'=>'customer@example.com', 'phone'=>'0901234567', 'address'=>'HCM', 'id'=>1, 'roles'=>'admin', 'status'=>0];
        $this->putJson('/api/profile', $body)->assertOk()->assertJsonPath('user.id',2)->assertJsonPath('user.roles','user')->assertJsonPath('user.status',1);
        $this->assertDatabaseHas('nguoidung', ['id'=>1,'name'=>'Admin']);
        $body['email']='admin@example.com';
        $this->putJson('/api/profile',$body)->assertUnprocessable();
    }

    public function test_my_orders_are_private_and_paginated(): void
    {
        $this->getJson('/api/my-orders')->assertUnauthorized();
        for($i=0;$i<11;$i++) DB::table('donhang')->insert(['user_id'=>2,'code'=>'MY-'.$i]);
        DB::table('donhang')->insert(['user_id'=>1,'code'=>'PRIVATE']);
        Sanctum::actingAs(User::findOrFail(2));
        $this->getJson('/api/my-orders?user_id=1')->assertOk()->assertJsonCount(10,'data')->assertJsonPath('meta.total',11)->assertJsonMissing(['code'=>'PRIVATE']);
        $this->getJson('/api/my-orders?page=2')->assertOk()->assertJsonCount(1,'data');
        $this->getJson('/api/my-orders?page=0')->assertUnprocessable();
    }

    public function test_blocked_account_cannot_use_an_existing_bearer_token(): void
    {
        $user=User::findOrFail(2);
        $token=$user->createToken('old')->plainTextToken;
        $user->status=0; $user->save();
        $this->withToken($token)->getJson('/api/user')->assertForbidden();
        $this->withToken($token)->getJson('/api/my-orders')->assertForbidden();
        $this->withToken($token)->putJson('/api/profile',[])->assertForbidden();
    }

    public function test_admin_cannot_block_another_admin(): void
    {
        DB::table('nguoidung')->insert(['id'=>3,'name'=>'Other Admin','roles'=>'admin','status'=>1]);
        Sanctum::actingAs(User::findOrFail(1));
        $this->patchJson('/api/admin/users/3/status',['status'=>0])->assertNotFound();
        $this->assertDatabaseHas('nguoidung',['id'=>3,'status'=>1]);
    }

    public function test_logout_revokes_current_token_only(): void
    {
        $user=User::findOrFail(2);
        $token=$user->createToken('current')->plainTextToken;
        $user->createToken('another-session');
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens',1);
        $this->assertNull(\Laravel\Sanctum\PersonalAccessToken::findToken($token));
    }
}
