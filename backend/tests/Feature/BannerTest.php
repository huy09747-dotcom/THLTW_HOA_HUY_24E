<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
class BannerTest extends TestCase {
 protected function setUp():void {parent::setUp();$this->assertSame(':memory:',config('database.connections.sqlite.database'));Schema::create('banner',function(Blueprint $t){$t->id();foreach(['name','description','image','link','position'] as $c)$t->string($c)->nullable();foreach(['status','sort_order','created_by','updated_by'] as $c)$t->integer($c)->nullable();$t->timestamps();$t->softDeletes();});Schema::create('nguoidung',function(Blueprint $t){$t->id();$t->string('roles');$t->integer('status');$t->softDeletes();});DB::table('nguoidung')->insert([['id'=>1,'roles'=>'admin','status'=>1],['id'=>2,'roles'=>'user','status'=>1]]);}
 private function data(array $extra=[]):array{return array_merge(['name'=>'Banner','description'=>'Description','image'=>'/images/test.png','link'=>'/products','position'=>'slideshow','sort_order'=>1,'status'=>1],$extra);}
 public function test_public_only_shows_active_slides_in_order():void {foreach([$this->data(['name'=>'Second','sort_order'=>2]),$this->data(['name'=>'First']),$this->data(['status'=>0]),$this->data(['position'=>'advertise']),$this->data(['deleted_at'=>now()])] as $row)DB::table('banner')->insert($row);$this->getJson('/api/banners')->assertOk()->assertJsonCount(2)->assertJsonPath('0.name','First')->assertJsonPath('1.name','Second');}
 public function test_admin_permissions_crud_and_link_validation():void {$this->getJson('/api/admin/banners')->assertUnauthorized();Sanctum::actingAs(User::findOrFail(2));$this->postJson('/api/admin/banners',$this->data())->assertForbidden();Sanctum::actingAs(User::findOrFail(1));$id=$this->postJson('/api/admin/banners',$this->data())->assertCreated()->json('data.id');$this->getJson('/api/admin/banners')->assertJsonCount(1,'data');foreach(['javascript:alert(1)','//evil.example','/\\evil.example'] as $link)$this->putJson('/api/admin/banners/'.$id,$this->data(['link'=>$link]))->assertUnprocessable();$this->putJson('/api/admin/banners/'.$id,$this->data(['status'=>0]))->assertOk();$this->getJson('/api/banners')->assertJsonCount(0);$this->deleteJson('/api/admin/banners/'.$id)->assertOk();$this->assertSoftDeleted('banner',['id'=>$id]);}
 public function test_seed_is_idempotent_and_preserves_edits():void {$this->seed(\Database\Seeders\BannerSeeder::class);DB::table('banner')->where('sort_order',1)->update(['name'=>'Edited']);$this->seed(\Database\Seeders\BannerSeeder::class);$this->assertDatabaseCount('banner',3);$this->assertDatabaseHas('banner',['name'=>'Edited']);}
}
