<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommerceTest extends TestCase
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
        Schema::table('nguoidung', function(Blueprint $t){$t->string('name')->nullable();});
        (require database_path('migrations/2026_10_26_000001_create_reviews_and_payments.php'))->up();
        (require database_path('migrations/2026_10_26_000002_add_review_visibility.php'))->up();
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


    public function test_review_auth_validation_ownership_and_average(): void {
        $this->putJson('/api/products/1/my-review',['rating'=>5,'comment'=>'Good'])->assertUnauthorized();
        Sanctum::actingAs(User::findOrFail(1));
        $this->putJson('/api/products/1/my-review',['rating'=>6,'comment'=>'Good'])->assertUnprocessable();
        $this->putJson('/api/products/1/my-review',['rating'=>5,'comment'=>'Good','user_id'=>2])->assertOk();
        $this->putJson('/api/products/1/my-review',['rating'=>4,'comment'=>'Updated'])->assertOk();
        $this->assertDatabaseCount('product_reviews',1);
        Sanctum::actingAs(User::findOrFail(2));
        $this->deleteJson('/api/products/1/my-review')->assertOk();
        $this->assertDatabaseCount('product_reviews',1);
        $this->putJson('/api/products/1/my-review',['rating'=>2,'comment'=>'Okay'])->assertOk();
        $this->getJson('/api/products/1/reviews')->assertJsonPath('summary.count',2)->assertJsonPath('summary.average',3);
        $this->getJson('/api/products/1/my-review')->assertJsonPath('data.rating',2);
        $this->getJson('/api/products/999/reviews')->assertNotFound();
    }
    public function test_checkout_queues_email_and_rejects_unconfigured_payment(): void {
        Sanctum::actingAs(User::findOrFail(1));
        config(['payments.momo.partner'=>null]);
        $this->postJson('/api/checkout',$this->shipping()+['payment_method'=>'momo'])->assertUnprocessable();
        $this->assertDatabaseCount('donhang',0);
        $this->assertDatabaseCount('chitietgiohang',2);
        $this->postJson('/api/checkout',$this->shipping()+['payment_method'=>'evil'])->assertUnprocessable();
        $this->postJson('/api/checkout',$this->shipping())->assertCreated()->assertJsonPath('data.email_status','queued');
        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\OrderConfirmation::class,fn($mail)=>$mail->hasTo('test@example.com'));
    }
    public function test_removed_vnpay_method_cannot_create_order(): void
    {
        Sanctum::actingAs(User::findOrFail(1));
        $this->postJson('/api/checkout', $this->shipping() + ['payment_method' => 'vnpay'])->assertUnprocessable();
        $this->assertDatabaseCount('donhang', 0);
        $this->assertDatabaseCount('chitietgiohang', 2);
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 5]);
    }
    public function test_removed_momo_method_cannot_create_order(): void
    {
        Sanctum::actingAs(User::findOrFail(1));
        $this->postJson('/api/checkout', $this->shipping() + ['payment_method' => 'momo'])->assertUnprocessable();
        $this->assertDatabaseCount('donhang', 0);
        $this->assertDatabaseCount('chitietgiohang', 2);
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 5]);
    }
    public function test_email_contains_order_snapshot(): void {
        Sanctum::actingAs(User::findOrFail(1));
        $id=$this->postJson('/api/checkout',$this->shipping())->assertCreated()->json('data.id');
        $mail=new \App\Mail\OrderConfirmation(\App\Models\Donhang::findOrFail($id));
        $html=$mail->render();
        $this->assertStringContainsString('Phone',$html);
        $this->assertStringContainsString('123 Test',$html);
        $this->assertStringContainsString('/orders/'.$id,$html);
    }
    public function test_removed_paypal_method_cannot_create_order(): void
    {
        Sanctum::actingAs(User::findOrFail(1));
        $this->postJson('/api/checkout', $this->shipping() + ['payment_method' => 'paypal'])->assertUnprocessable();
        $this->assertDatabaseCount('donhang', 0);
        $this->assertDatabaseCount('chitietgiohang', 2);
        $this->assertDatabaseHas('sanpham', ['id' => 1, 'qty' => 5]);
    }

}
