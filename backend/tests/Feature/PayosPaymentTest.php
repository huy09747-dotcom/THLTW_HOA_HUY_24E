<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PayosPaymentTest extends TestCase
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

    private function sign(array $data): string
    {
        ksort($data);
        return hash_hmac('sha256', implode('&', array_map(fn ($key) => $key.'='.$data[$key], array_keys($data))), 'checksum');
    }

    private function preparePayment(): int
    {
        config(['payments.payos.client' => 'client', 'payments.payos.key' => 'key',
            'payments.payos.checksum' => 'checksum', 'payments.frontend' => 'http://localhost:3000']);
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        Sanctum::actingAs(User::findOrFail(1));
        return DB::table('donhang')->insertGetId([
            'user_id' => 1, 'status' => 1, 'subtotal' => 10000, 'discount' => 0,
            'total' => 10000, 'payment_method' => 'payos', 'payment_status' => 'unpaid', 'code' => 'HH1',
        ]);
    }

    private function attempt(int $id): object
    {
        DB::table('payment_attempts')->insert([
            'order_id' => $id, 'reference' => '1234567890123', 'method' => 'payos',
            'amount' => 10000, 'currency' => 'VND', 'status' => 'pending', 'gateway_id' => 'abc',
        ]);
        return DB::table('payment_attempts')->where('order_id', $id)->first();
    }

    public function test_customer_cancel_checks_owner_status_and_restocks_only_once(): void
    {
        $id = $this->preparePayment();
        DB::table('chitietdonhang')->insert([
            'order_id'=>$id, 'product_id'=>1, 'price'=>100, 'qty'=>2, 'amount'=>200,
        ]);
        Sanctum::actingAs(User::findOrFail(2));
        $this->postJson('/api/my-orders/'.$id.'/cancel')->assertNotFound();
        Sanctum::actingAs(User::findOrFail(1));
        DB::table('donhang')->where('id',$id)->update(['status'=>2]);
        $this->postJson('/api/my-orders/'.$id.'/cancel')->assertUnprocessable();
        DB::table('donhang')->where('id',$id)->update(['status'=>1, 'payment_status'=>'paid']);
        $this->postJson('/api/my-orders/'.$id.'/cancel')->assertUnprocessable();
        DB::table('donhang')->where('id',$id)->update(['payment_status'=>'unpaid']);
        $this->postJson('/api/my-orders/'.$id.'/cancel')->assertOk();
        $this->postJson('/api/my-orders/'.$id.'/cancel')->assertOk();
        $this->assertDatabaseHas('sanpham',['id'=>1,'qty'=>7]);
        $this->assertDatabaseHas('donhang',['id'=>$id,'status'=>5]);
    }

    public function test_start_uses_database_amount_and_reuses_link(): void
    {
        $id = $this->preparePayment();
        \Illuminate\Support\Facades\Http::fake(function ($request) {
            if ($request->method() === 'GET') return \Illuminate\Support\Facades\Http::response(['code'=>'00','data'=>['status'=>'PENDING']]);
            $this->assertSame(10000, $request['amount']);
            $body = $request->data();
            unset($body['signature']);
            $this->assertSame($this->sign($body), $request['signature']);
            $data = ['orderCode' => $request['orderCode'], 'amount' => 10000,
                'checkoutUrl' => 'https://pay.payos.vn/web/abc', 'paymentLinkId' => 'abc'];
            return \Illuminate\Support\Facades\Http::response(['code' => '00', 'data' => $data, 'signature' => $this->sign($data)]);
        });
        $this->postJson('/api/my-orders/'.$id.'/payment', ['amount' => 1])->assertOk()
            ->assertJsonPath('data.amount', 10000);
        $this->postJson('/api/my-orders/'.$id.'/payment')->assertOk();
        \Illuminate\Support\Facades\Http::assertSentCount(2);
        Sanctum::actingAs(User::findOrFail(2));
        $this->postJson('/api/my-orders/'.$id.'/payment')->assertNotFound();
    }

    public function test_webhook_rejects_forgery_wrong_amount_and_handles_replay(): void
    {
        $id = $this->preparePayment();
        $a = $this->attempt($id);
        $data = ['orderCode' => (int) $a->reference, 'amount' => 10000, 'currency' => 'VND',
            'paymentLinkId' => 'abc', 'code' => '00'];
        $this->postJson('/api/payments/payos/webhook', ['data' => $data, 'signature' => 'fake'])->assertStatus(400);
        $wrong = array_replace($data, ['amount' => 1]);
        $this->postJson('/api/payments/payos/webhook', ['data' => $wrong, 'signature' => $this->sign($wrong)])->assertStatus(400);
        $this->assertDatabaseHas('donhang', ['id' => $id, 'payment_status' => 'unpaid']);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response([
            'code' => '00', 'data' => ['id' => 'abc', 'orderCode' => (int) $a->reference,
                'amount' => 10000, 'amountPaid' => 10000, 'amountRemaining' => 0, 'status' => 'PAID'],
        ])]);
        $payload = ['data' => $data, 'signature' => $this->sign($data)];
        $this->postJson('/api/payments/payos/webhook', $payload)->assertOk();
        $this->postJson('/api/payments/payos/webhook', $payload)->assertOk();
        $this->assertDatabaseHas('donhang', ['id' => $id, 'payment_status' => 'paid', 'status' => 2]);
        \Illuminate\Support\Facades\Http::assertSentCount(1);
    }

    public function test_payment_does_not_reopen_cancelled_or_regress_shipping_orders(): void
    {
        $id = $this->preparePayment();
        $a = $this->attempt($id);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response([
            'code' => '00', 'data' => ['id' => 'abc', 'orderCode' => (int) $a->reference,
                'amount' => 10000, 'amountPaid' => 10000, 'amountRemaining' => 0, 'status' => 'PAID'],
        ])]);
        foreach ([3, 4, 5] as $status) {
            DB::table('donhang')->where('id', $id)->update(['status' => $status, 'payment_status' => 'unpaid']);
            DB::table('payment_attempts')->where('id', $a->id)->update(['status' => 'pending']);
            app(\App\Services\PayosPayment::class)->verify(\App\Models\Donhang::findOrFail($id));
            $this->assertDatabaseHas('donhang', ['id' => $id, 'status' => $status, 'payment_status' => 'paid']);
        }
    }

    public function test_return_parameters_cannot_mark_an_unpaid_order_paid(): void
    {
        $id = $this->preparePayment();
        $a = $this->attempt($id);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response([
            'code' => '00', 'data' => ['id' => 'abc', 'orderCode' => (int) $a->reference,
                'amount' => 10000, 'amountPaid' => 0, 'amountRemaining' => 10000, 'status' => 'PENDING'],
        ])]);
        $this->postJson('/api/my-orders/'.$id.'/payment/verify?status=PAID&code=00')->assertOk()
            ->assertJsonPath('data.payment_status', 'unpaid');
    }

    public function test_wrong_gateway_amount_is_rejected(): void
    {
        $id = $this->preparePayment();
        $a = $this->attempt($id);
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response([
            'code' => '00', 'data' => ['id' => 'abc', 'orderCode' => (int) $a->reference,
                'amount' => 10000, 'amountPaid' => 9000, 'amountRemaining' => 0, 'status' => 'PAID'],
        ])]);
        $this->postJson('/api/my-orders/'.$id.'/payment/verify')->assertStatus(409);
        $this->assertDatabaseHas('donhang', ['id' => $id, 'payment_status' => 'unpaid']);
    }
    public function test_cancelled_payment_restarts_and_returns_to_original_origin(): void
    {
        $id = $this->preparePayment();
        $a = $this->attempt($id);
        DB::table('payment_attempts')->where('id', $a->id)->update(['url'=>'https://pay.payos.vn/web/abc']);
        \Illuminate\Support\Facades\Http::fake(function ($request) use ($id, $a) {
            if ($request->method() === 'GET') return \Illuminate\Support\Facades\Http::response(['code'=>'00','data'=>['status'=>'CANCELLED']]);
            $this->assertNotSame($a->reference, (string) $request['orderCode']);
            $this->assertSame('http://127.0.0.1:3000/orders/'.$id, $request['cancelUrl']);
            $data = ['orderCode'=>$request['orderCode'],'amount'=>10000,'checkoutUrl'=>'https://pay.payos.vn/web/new','paymentLinkId'=>'new'];
            return \Illuminate\Support\Facades\Http::response(['code'=>'00','data'=>$data,'signature'=>$this->sign($data)]);
        });
        $this->postJson('/api/my-orders/'.$id.'/payment', ['return_origin'=>'http://127.0.0.1:3000'])->assertOk();
        $this->assertDatabaseCount('donhang', 1);
        $this->assertDatabaseHas('donhang', ['id'=>$id,'payment_status'=>'unpaid']);
    }

    public function test_external_return_origin_is_rejected(): void
    {
        $id = $this->preparePayment();
        $this->postJson('/api/my-orders/'.$id.'/payment', ['return_origin'=>'https://evil.example'])->assertUnprocessable();
        $this->getJson('/api/payment-methods')->assertJsonCount(2, 'data');
    }
}
