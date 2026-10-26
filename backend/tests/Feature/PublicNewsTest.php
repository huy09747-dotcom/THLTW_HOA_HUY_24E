<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PublicNewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('nguoidung', function (Blueprint $table) {
            $table->id();
            $table->string('roles');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });
        foreach (['chudebaiviet', 'danhmuc', 'thuonghieu'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->softDeletes();
            });
        }
        foreach (['baiviet', 'menu'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                if ($name === 'baiviet') {
                    $table->integer('topic_id')->nullable();
                    $table->string('title');
                    $table->string('slug');
                    $table->text('detail');
                    $table->string('image')->nullable();
                    $table->text('description')->nullable();
                    $table->string('post_type')->default('post');
                } else {
                    $table->string('name');
                    $table->string('link');
                    $table->string('type');
                    $table->integer('table_id')->nullable();
                }
                $table->integer('status');
                $table->integer('created_by')->default(1);
                $table->integer('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        Schema::table('chudebaiviet',function(Blueprint $t){$t->string('slug')->nullable();$t->integer('status')->default(1);});
        DB::table('nguoidung')->insert([
            ['id' => 1, 'roles' => 'admin', 'status' => 1],
            ['id' => 2, 'roles' => 'user', 'status' => 1],
        ]);
    }


 public function test_only_published_posts_in_active_topics_are_public(): void {
  DB::table('chudebaiviet')->insert([['id'=>1,'name'=>'Tin tức','slug'=>'tin-tuc','status'=>1],['id'=>2,'name'=>'Sự kiện','slug'=>'su-kien','status'=>1],['id'=>3,'name'=>'Hidden','slug'=>'hidden','status'=>0]]);
  foreach([['news',1,1,'post'],['event',2,1,'post'],['draft',1,2,'post'],['hidden-topic',3,1,'post'],['page',1,1,'page']] as [$slug,$topic,$status,$type])DB::table('baiviet')->insert(['slug'=>$slug,'title'=>$slug,'detail'=>'Content','topic_id'=>$topic,'status'=>$status,'post_type'=>$type]);
  $this->getJson('/api/news')->assertOk()->assertJsonCount(2,'data');
  $this->getJson('/api/news?topic=su-kien')->assertJsonCount(1,'data')->assertJsonPath('data.0.slug','event');
  $this->getJson('/api/news/news')->assertOk();
  foreach(['draft','hidden-topic','page','missing'] as $slug)$this->getJson('/api/news/'.$slug)->assertNotFound();
  $this->getJson('/api/news-topics')->assertJsonCount(2,'data');
 }
}
