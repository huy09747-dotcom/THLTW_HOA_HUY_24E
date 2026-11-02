<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminReviewTest extends TestCase
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
        Schema::table('nguoidung', function(Blueprint $t){$t->string('name')->nullable();$t->string('roles')->default('user');});
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



 private function prepareReviews():void {DB::table('nguoidung')->where('id',1)->update(['roles'=>'admin']);DB::table('product_reviews')->insert([['product_id'=>1,'user_id'=>1,'rating'=>5,'comment'=>'Excellent','is_visible'=>1],['product_id'=>1,'user_id'=>2,'rating'=>1,'comment'=>'Needs work','is_visible'=>1]]);}
 public function test_management_requires_admin():void {
  $this->getJson('/api/admin/reviews')->assertUnauthorized();Sanctum::actingAs(User::findOrFail(2));
  $this->getJson('/api/admin/reviews')->assertForbidden();$this->patchJson('/api/admin/reviews/1',['is_visible'=>false])->assertForbidden();$this->deleteJson('/api/admin/reviews/1')->assertForbidden();
 }
 public function test_hide_restore_filters_delete_and_public_average():void {
  $this->prepareReviews();Sanctum::actingAs(User::findOrFail(1));
  $id=DB::table('product_reviews')->where('user_id',2)->value('id');
  $this->getJson('/api/admin/reviews')->assertOk()->assertJsonPath('summary.total',2);
  $this->getJson('/api/admin/reviews?search=Phone&rating=1')->assertJsonCount(1,'data');
  $this->patchJson('/api/admin/reviews/'.$id,['is_visible'=>false])->assertOk();
  $this->getJson('/api/products/1/reviews')->assertJsonPath('summary.count',1)->assertJsonPath('summary.average',5)->assertJsonMissing(['comment'=>'Needs work']);
  $this->getJson('/api/admin/reviews?visibility=hidden')->assertJsonCount(1,'data');
  Sanctum::actingAs(User::findOrFail(2));
  $this->getJson('/api/products/1/my-review')->assertOk()->assertJsonPath('data',null);
  $this->putJson('/api/products/1/my-review',['rating'=>4,'comment'=>'Edited','is_visible'=>true])->assertOk();
  $this->assertDatabaseHas('product_reviews',['id'=>$id,'is_visible'=>1,'comment'=>'Edited']);
  Sanctum::actingAs(User::findOrFail(1));$this->patchJson('/api/admin/reviews/'.$id,['is_visible'=>true])->assertOk();
  $this->getJson('/api/products/1/reviews')->assertJsonPath('summary.count',2)->assertJsonPath('summary.average',4.5);
  $this->deleteJson('/api/admin/reviews/'.$id)->assertOk();$this->assertDatabaseCount('product_reviews',1);
  $this->patchJson('/api/admin/reviews/999',['is_visible'=>true])->assertNotFound();$this->getJson('/api/admin/reviews?rating=6')->assertUnprocessable();
 }
}
