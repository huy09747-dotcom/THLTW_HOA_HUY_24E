<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordLink;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('sqlite', config('database.default'));
        Notification::fake();
        Schema::create('nguoidung', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique();
            $t->string('password'); $t->string('remember_token')->nullable();
            $t->integer('status')->default(1); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('password_reset_tokens', function (Blueprint $t) {
            $t->string('email')->primary(); $t->string('token'); $t->timestamp('created_at')->nullable();
        });
        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id(); $t->morphs('tokenable'); $t->string('name'); $t->string('token',64)->unique();
            $t->text('abilities')->nullable(); $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable(); $t->timestamps();
        });
        $u = new User(['name'=>'Test', 'email'=>'test@example.com', 'password'=>'old-password']);
        $u->status=1; $u->save();
    }

    public function test_request_is_generic_and_only_notifies_active_registered_user(): void
    {
        $response=$this->postJson('/api/forgot-password',['email'=>'test@example.com'])->assertOk()->json();
        Notification::assertSentTo(User::first(), ResetPasswordLink::class);
        $this->assertSame($response,$this->postJson('/api/forgot-password',['email'=>'missing@example.com'])->assertOk()->json());
        $this->assertSame($response,$this->postJson('/api/forgot-password',['email'=>'test@example.com'])->assertOk()->json());
        Notification::assertSentTimes(ResetPasswordLink::class,1);
        $stored=DB::table('password_reset_tokens')->first();
        Notification::assertSentTo(User::first(), ResetPasswordLink::class, function ($n) use ($stored) {
            $this->assertNotSame($n->token,$stored->token);
            return Hash::check($n->token,$stored->token);
        });
    }

    public function test_reset_requires_valid_token_revokes_sessions_and_cannot_be_replayed(): void
    {
        $user=User::first(); $user->createToken('old-session');
        $token=Password::broker('users')->createToken($user);
        $body=['email'=>$user->email,'token'=>'bad','password'=>'new-password','password_confirmation'=>'new-password'];
        $this->postJson('/api/reset-password',$body)->assertStatus(422);
        $this->assertTrue(Hash::check('old-password',$user->fresh()->password));
        $body['token']=$token;
        $this->postJson('/api/reset-password',array_merge($body,['password_confirmation'=>'different']))->assertStatus(422);
        $this->postJson('/api/reset-password',$body)->assertOk();
        $this->assertTrue(Hash::check('new-password',$user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens',0);
        $this->assertDatabaseCount('password_reset_tokens',0);
        $this->postJson('/api/reset-password',$body)->assertStatus(422);
    }

    public function test_expired_and_blocked_accounts_cannot_reset(): void
    {
        $user=User::first(); $token=Password::broker('users')->createToken($user);
        $body=['email'=>$user->email,'token'=>$token,'password'=>'new-password','password_confirmation'=>'new-password'];
        DB::table('password_reset_tokens')->update(['created_at'=>now()->subMinutes(61)]);
        $this->postJson('/api/reset-password',$body)->assertStatus(422);
        DB::table('password_reset_tokens')->update(['created_at'=>now()]);
        $user->status=0; $user->save();
        $this->postJson('/api/reset-password',$body)->assertStatus(422);
        $this->postJson('/api/forgot-password',['email'=>$user->email])->assertOk();
        Notification::assertNothingSent();
    }
}
