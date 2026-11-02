<?php

namespace Tests\Feature;

use App\Mail\OrderStatusChanged;
use App\Models\Donhang;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopFeaturesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite',config('database.default'));
        $this->assertSame(':memory:',config('database.connections.sqlite.database'));
        Mail::fake();
        foreach ([
            'nguoidung'=>['status'], 'giohang'=>['nguoidung_id'],
            'chitietgiohang'=>['giohang_id','sanpham_id','soluong'],
            'sanpham'=>['status','is_available','price_sale','price_buy','discount_percent','qty'],
            'danhmuc'=>['status'], 'thuonghieu'=>['status'],
            'donhang'=>['user_id','status','subtotal','discount','total'],
            'chitietdonhang'=>['order_id','product_id','price','qty','amount'],
        ] as $name=>$columns) {
            Schema::create($name,function(Blueprint $t) use($columns,$name) {
                $t->id(); foreach($columns as $c) $t->integer($c)->default(0);
                $strings=match($name) {
                    'nguoidung'=>['name','email','roles'],
                    'sanpham'=>['name','slug','image'],
                    'donhang'=>['name','phone','email','address','note','code','order_type','payment_method','payment_status'],
                    'chitietdonhang'=>['item_name'], default=>[],
                };
                foreach($strings as $c) $t->string($c)->nullable();
                $t->timestamps(); $t->softDeletes();
            });
        }
        Schema::create('product_reviews', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('product_id'); $t->unsignedBigInteger('user_id');
            $t->integer('rating'); $t->boolean('is_visible')->default(true);
        });
        (require database_path('migrations/2026_11_02_000001_create_shop_features.php'))->up();
        DB::table('nguoidung')->insert([
            ['id'=>1,'status'=>1,'roles'=>'user','name'=>'Customer','email'=>'test@example.com'],
            ['id'=>2,'status'=>1,'roles'=>'user','name'=>'Other','email'=>'other@example.com'],
            ['id'=>3,'status'=>1,'roles'=>'admin','name'=>'Admin','email'=>'admin@example.com'],
        ]);
        DB::table('sanpham')->insert(['id'=>1,'name'=>'Phone','slug'=>'phone','status'=>1,'is_available'=>1,'price_sale'=>10000,'price_buy'=>5000,'qty'=>5]);
        DB::table('giohang')->insert(['id'=>1,'nguoidung_id'=>1]);
        DB::table('chitietgiohang')->insert(['giohang_id'=>1,'sanpham_id'=>1,'soluong'=>2]);
        Sanctum::actingAs(User::find(1));
    }

    private function coupon(array $changes=[]): int
    {
        return DB::table('coupons')->insertGetId(array_merge([
            'code'=>'SAVE10','type'=>'percent','value'=>10,'min_order'=>10000,'active'=>1,
        ],$changes));
    }

    private function shipping(): array
    {
        return ['name'=>'Customer','phone'=>'0901234567','email'=>'test@example.com','address'=>'Test address','payment_method'=>'cod'];
    }

    public function test_product_card_rating_counts_only_visible_reviews(): void
    {
        DB::table('product_reviews')->insert([
            ['product_id'=>1,'user_id'=>1,'rating'=>5,'is_visible'=>true],
            ['product_id'=>1,'user_id'=>2,'rating'=>4,'is_visible'=>true],
            ['product_id'=>1,'user_id'=>3,'rating'=>1,'is_visible'=>false],
        ]);
        $this->getJson('/api/products')->assertOk()
            ->assertJsonPath('data.0.review_count',2)->assertJsonPath('data.0.review_average',4.5);
        $this->getJson('/api/products/phone')->assertOk()->assertJsonPath('data.review_count',2);
        $this->putJson('/api/favorites/1')->assertOk();
        $this->getJson('/api/favorites')->assertOk()->assertJsonPath('data.0.review_average',4.5);
        DB::table('product_reviews')->update(['is_visible'=>false]);
        $this->getJson('/api/products')->assertOk()->assertJsonPath('data.0.review_count',0)
            ->assertJsonPath('data.0.review_average',0);
    }

    public function test_coupon_preview_and_checkout_use_server_prices_and_record_usage(): void
    {
        $this->coupon(['max_discount'=>1500]);
        $this->postJson('/api/coupons/preview',['coupon_code'=>'save10','subtotal'=>1])->assertOk()->assertJsonPath('data.discount',1500)->assertJsonPath('data.total',18500);
        $result=$this->postJson('/api/checkout',$this->shipping()+['coupon_code'=>'save10','total'=>1,'discount'=>19999])->assertCreated();
        $id=$result->json('data.id');
        $this->assertDatabaseHas('donhang',['id'=>$id,'subtotal'=>20000,'discount'=>1500,'total'=>18500,'status'=>1]);
        $this->assertDatabaseHas('coupon_usages',['order_id'=>$id,'discount'=>1500]);
        $this->assertDatabaseHas('sanpham',['id'=>1,'qty'=>3]);
    }

    public function test_invalid_expired_minimum_and_exhausted_coupons_cannot_checkout(): void
    {
        $id=$this->coupon(['ends_at'=>now()->subMinute()]);
        $body=$this->shipping()+['coupon_code'=>'SAVE10'];
        $this->postJson('/api/checkout',$body)->assertUnprocessable();
        DB::table('coupons')->where('id',$id)->update(['ends_at'=>null,'min_order'=>99999]);
        $this->postJson('/api/checkout',$body)->assertUnprocessable();
        DB::table('coupons')->where('id',$id)->update(['min_order'=>0,'usage_limit'=>1]);
        DB::table('coupon_usages')->insert(['coupon_id'=>$id,'user_id'=>2,'order_id'=>99,'discount'=>1000]);
        $this->postJson('/api/checkout',$body)->assertUnprocessable();
        $this->assertDatabaseCount('donhang',0);
        $this->assertDatabaseHas('sanpham',['id'=>1,'qty'=>5]);
        $this->assertDatabaseCount('chitietgiohang',1);
    }

    public function test_fixed_discount_is_capped_and_date_rules_are_enforced(): void
    {
        $this->coupon(['type'=>'fixed','value'=>50000]);
        $this->postJson('/api/coupons/preview',['coupon_code'=>'SAVE10'])->assertOk()->assertJsonPath('data.discount',20000)->assertJsonPath('data.total',0);
        Sanctum::actingAs(User::find(3));
        $body=['code'=>'DATES','type'=>'fixed','value'=>1000,'min_order'=>0,'active'=>true,
            'starts_at'=>null,'ends_at'=>now()->addDay()->toIso8601String()];
        $this->postJson('/api/admin/coupons',$body)->assertOk();
        $this->postJson('/api/admin/coupons',array_merge($body,['code'=>'BADDATE','starts_at'=>now()->addDays(2)->toIso8601String()]))->assertUnprocessable();
    }

    public function test_admin_coupon_management_and_dashboard_require_admin(): void
    {
        $this->getJson('/api/admin/dashboard')->assertForbidden();
        $this->getJson('/api/admin/coupons')->assertForbidden();
        Sanctum::actingAs(User::find(3));
        $body=['code'=>'NEW10','type'=>'percent','value'=>101,'min_order'=>0,'active'=>true];
        $this->postJson('/api/admin/coupons',$body)->assertUnprocessable();
        $body['value']=10;
        $this->postJson('/api/admin/coupons',$body)->assertOk();
        $id=DB::table('coupons')->value('id');
        $this->putJson('/api/admin/coupons/'.$id,array_merge($body,['active'=>false]))->assertOk();
        $this->assertDatabaseHas('coupons',['id'=>$id,'active'=>0]);
        $this->getJson('/api/admin/coupons')->assertOk()->assertJsonPath('data.0.used',0);
    }

    public function test_favorites_are_idempotent_and_separate_for_each_account(): void
    {
        $this->putJson('/api/favorites/1')->assertOk();
        $this->putJson('/api/favorites/1')->assertOk();
        $this->assertDatabaseCount('favorites',1);
        $this->getJson('/api/favorites')->assertOk()->assertJsonPath('data.0.id',1);
        Sanctum::actingAs(User::find(2));
        $this->getJson('/api/favorites/ids')->assertJsonCount(0,'data');
        $this->deleteJson('/api/favorites/1')->assertOk();
        $this->assertDatabaseCount('favorites',1);
        $this->putJson('/api/favorites/999')->assertNotFound();
        Sanctum::actingAs(User::find(1));
        DB::table('sanpham')->where('id',1)->update(['status'=>0]);
        $this->getJson('/api/favorites')->assertJsonCount(0,'data');
        $this->deleteJson('/api/favorites/1')->assertOk();
        $this->assertDatabaseCount('favorites',0);
    }

    public function test_dashboard_excludes_cancelled_unpaid_and_out_of_range_orders(): void
    {
        foreach([[4,'paid',100],[5,'paid',200],[4,'unpaid',300],[2,'paid',400]] as [$status,$payment,$total]) {
            DB::table('donhang')->insert(['user_id'=>1,'status'=>$status,'payment_status'=>$payment,'total'=>$total,'created_at'=>now()]);
        }
        DB::table('donhang')->insert(['user_id'=>1,'status'=>4,'payment_status'=>'paid','total'=>500,'created_at'=>now()->subYears(2)]);
        Sanctum::actingAs(User::find(3));
        $this->getJson('/api/admin/dashboard')->assertOk()->assertJsonPath('data.revenue',100)->assertJsonPath('data.orders',4)->assertJsonPath('data.cancelled',1);
    }

    public function test_status_email_is_sent_after_commit_not_on_replay_or_rollback(): void
    {
        $order=Donhang::create(['user_id'=>1,'name'=>'Customer','email'=>'test@example.com','status'=>1,'payment_status'=>'unpaid','payment_method'=>'cod','total'=>10000]);
        DB::beginTransaction();
        $order->status=2; $order->save();
        Mail::assertNothingQueued();
        DB::rollBack();
        Mail::assertNothingQueued();
        $order->refresh();
        $order->status=2; $order->save();
        Mail::assertQueued(OrderStatusChanged::class, fn($mail)=>$mail->snapshot['status']===2 && $mail->hasTo('test@example.com'));
        $order->status=2; $order->save();
        Mail::assertQueued(OrderStatusChanged::class,1);
        Sanctum::actingAs(User::find(3));
        $this->patchJson('/api/donhangs/'.$order->id.'/status',['status'=>4])->assertOk();
        $this->assertDatabaseHas('donhang',['id'=>$order->id,'payment_status'=>'paid','status'=>4]);
        Mail::assertQueued(OrderStatusChanged::class,2);
        $this->patchJson('/api/donhangs/'.$order->id.'/status',['status'=>4])->assertOk();
        Mail::assertQueued(OrderStatusChanged::class,2);
    }
}
