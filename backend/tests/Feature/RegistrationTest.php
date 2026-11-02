<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('nguoidung', function (Blueprint $t) {
            $t->id();
            foreach (['name','email','phone','username','password','roles'] as $field) $t->string($field);
            $t->string('address')->nullable();
            $t->string('remember_token')->nullable();
            $t->integer('status');
            $t->timestamps();
            $t->softDeletes();
        });
        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id(); $t->morphs('tokenable'); $t->string('name'); $t->string('token',64)->unique();
            $t->text('abilities')->nullable(); $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable(); $t->timestamps();
        });
    }

    public function test_registration_without_username_can_login_by_email(): void
    {
        $body=['name'=>'Test User','email'=>'test@example.com','phone'=>'0901234567',
            'password'=>'test-password','password_confirmation'=>'test-password'];
        $result=$this->postJson('/api/register',$body)->assertCreated();
        $this->assertStringStartsWith('user_', $result->json('user.username'));
        $this->postJson('/api/login',['email'=>$body['email'],'password'=>$body['password'],'audience'=>'user'])
            ->assertOk()->assertJsonPath('user.email',$body['email']);
        $this->postJson('/api/register',array_merge($body,['email'=>'second@example.com','phone'=>'0901234568']))
            ->assertCreated();
        $this->assertDatabaseCount('nguoidung',2);
        $this->assertNotSame($result->json('user.username'), \App\Models\User::find(2)->username);
        $this->postJson('/api/register',$body)->assertUnprocessable();
    }
}
