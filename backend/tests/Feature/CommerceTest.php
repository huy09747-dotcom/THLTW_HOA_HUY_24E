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
    private function onlineOrder(string $method): int {
        config(['payments.vnpay.tmn'=>'TEST','payments.vnpay.secret'=>'SECRET','payments.momo.partner'=>'PARTNER','payments.momo.access'=>'ACCESS','payments.momo.secret'=>'SECRET','payments.paypal.client'=>'CLIENT','payments.paypal.secret'=>'SECRET','payments.paypal.vnd_per_usd'=>100]);
        Sanctum::actingAs(User::findOrFail(1));
        return $this->postJson('/api/checkout',$this->shipping()+['payment_method'=>$method])->assertCreated()->json('data.id');
    }
    public function test_vnpay_signature_amount_ownership_and_replay(): void {
        $id=$this->onlineOrder('vnpay');
        $first=$this->postJson('/api/my-orders/'.$id.'/payment')->assertOk()->json('data.url');
        $this->postJson('/api/my-orders/'.$id.'/payment')->assertJsonPath('data.url',$first);
        Sanctum::actingAs(User::findOrFail(2));
        $this->postJson('/api/my-orders/'.$id.'/payment')->assertNotFound();
        $a=DB::table('payment_attempts')->first();
        $data=['vnp_TxnRef'=>$a->reference,'vnp_TmnCode'=>'TEST','vnp_Amount'=>'20000','vnp_ResponseCode'=>'00','vnp_TransactionStatus'=>'00'];
        $this->getJson('/api/payments/vnpay/ipn?'.http_build_query($data+['vnp_SecureHash'=>'bad']))->assertJsonPath('RspCode','97');
        $sign=function($d){ksort($d);return $d+['vnp_SecureHash'=>hash_hmac('sha512',http_build_query($d,'','&',PHP_QUERY_RFC1738),'SECRET')];};
        $this->getJson('/api/payments/vnpay/ipn?'.http_build_query($sign(array_replace($data,['vnp_Amount'=>'1']))))->assertJsonPath('RspCode','04');
        $this->assertDatabaseHas('donhang',['id'=>$id,'payment_status'=>'unpaid']);
        $this->getJson('/api/payments/vnpay/ipn?'.http_build_query($sign($data)))->assertJsonPath('RspCode','00');
        $this->getJson('/api/payments/vnpay/ipn?'.http_build_query($sign($data)))->assertJsonPath('RspCode','02');
        $this->assertDatabaseHas('donhang',['id'=>$id,'payment_status'=>'paid']);
    }
    public function test_momo_only_signed_matching_success_settles_order(): void {
        $id=$this->onlineOrder('momo');
        \Illuminate\Support\Facades\Http::fake(['*'=>\Illuminate\Support\Facades\Http::response(['resultCode'=>0,'payUrl'=>'https://test-payment.momo.vn/pay'])]);
        $this->postJson('/api/my-orders/'.$id.'/payment')->assertOk();
        $a=DB::table('payment_attempts')->first();
        $data=['partnerCode'=>'PARTNER','orderId'=>$a->reference,'requestId'=>$a->reference,'amount'=>200,'extraData'=>'','message'=>'Success','orderInfo'=>'Order','orderType'=>'momo_wallet','payType'=>'qr','responseTime'=>123,'resultCode'=>0,'transId'=>123];
        $this->postJson('/api/payments/momo/ipn',$data+['signature'=>'fake'])->assertStatus(400);
        $sign=function($d){$signed=$d+['accessKey'=>'ACCESS'];ksort($signed);return $d+['signature'=>hash_hmac('sha256',implode('&',array_map(fn($k)=>$k.'='.$signed[$k],array_keys($signed))),'SECRET')];};
        $this->postJson('/api/payments/momo/ipn',$sign(array_replace($data,['amount'=>1])))->assertStatus(400);
        $this->postJson('/api/payments/momo/ipn',$sign($data))->assertNoContent();
        $this->postJson('/api/payments/momo/ipn',$sign($data))->assertNoContent();
        $this->assertDatabaseHas('donhang',['id'=>$id,'payment_status'=>'paid']);
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
    public function test_paypal_verifies_server_capture_amount_and_reference(): void {
        $id=$this->onlineOrder('paypal');
        \Illuminate\Support\Facades\Http::fake([
            '*oauth2/token'=>\Illuminate\Support\Facades\Http::response(['access_token'=>'TOKEN']),
            '*checkout/orders'=>\Illuminate\Support\Facades\Http::response(['id'=>'PAYPAL1','links'=>[['rel'=>'payer-action','href'=>'https://www.sandbox.paypal.com/checkoutnow?token=PAYPAL1']]]),
        ]);
        $this->postJson('/api/my-orders/'.$id.'/payment')->assertOk();
        $a=DB::table('payment_attempts')->first();
        $response=['id'=>'PAYPAL1','status'=>'COMPLETED','purchase_units'=>[['custom_id'=>$a->reference,'payments'=>['captures'=>[['status'=>'COMPLETED','amount'=>['currency_code'=>'USD','value'=>'0.01']]]]]]];
        \Illuminate\Support\Facades\Http::fake(['*oauth2/token'=>\Illuminate\Support\Facades\Http::response(['access_token'=>'TOKEN']),'*orders/PAYPAL1'=>\Illuminate\Support\Facades\Http::response($response)]);
        $this->postJson('/api/my-orders/'.$id.'/payment/verify')->assertUnprocessable();
        $response['purchase_units'][0]['payments']['captures'][0]['amount']['value']='2.00';
        \Illuminate\Support\Facades\Http::swap(new \Illuminate\Http\Client\Factory());
        \Illuminate\Support\Facades\Http::fake(['*oauth2/token'=>\Illuminate\Support\Facades\Http::response(['access_token'=>'TOKEN']),'*orders/PAYPAL1'=>\Illuminate\Support\Facades\Http::response($response)]);
        $this->postJson('/api/my-orders/'.$id.'/payment/verify')->assertOk()->assertJsonPath('data.payment_status','paid');
    }
}
