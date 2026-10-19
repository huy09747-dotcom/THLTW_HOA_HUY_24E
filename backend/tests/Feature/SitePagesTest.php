<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SitePagesTest extends TestCase
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
        (require database_path('migrations/2026_10_19_000001_create_site_settings_table.php'))->up();
        DB::table('nguoidung')->insert([
            ['id' => 1, 'roles' => 'admin', 'status' => 1],
            ['id' => 2, 'roles' => 'user', 'status' => 1],
        ]);
    }


    private function admin(): void { Sanctum::actingAs(User::findOrFail(1)); }
    private function pageData(): array { return ['title'=>'Giới thiệu', 'detail'=>'Nội dung mới', 'description'=>'Mô tả', 'status'=>1]; }

    public function test_only_active_admin_can_edit_pages_and_settings(): void
    {
        foreach(['/api/admin/pages', '/api/admin/site-settings'] as $url) $this->getJson($url)->assertUnauthorized();
        $this->putJson('/api/admin/pages/about',$this->pageData())->assertUnauthorized();
        $this->putJson('/api/admin/site-settings',['site_name'=>'Shop'])->assertUnauthorized();
        Sanctum::actingAs(User::findOrFail(2));
        foreach(['/api/admin/pages', '/api/admin/site-settings'] as $url) $this->getJson($url)->assertForbidden();
        $this->putJson('/api/admin/pages/about',$this->pageData())->assertForbidden();
        $this->putJson('/api/admin/site-settings',['site_name'=>'Shop'])->assertForbidden();
        $user=User::findOrFail(1);$user->status=0;Sanctum::actingAs($user);
        $this->putJson('/api/admin/pages/about',$this->pageData())->assertForbidden();
    }

    public function test_three_pages_can_be_saved_and_read_publicly(): void
    {
        $this->admin();
        $this->getJson('/api/admin/pages')->assertOk()->assertJsonCount(3,'data');
        foreach(['about','purchase-policy','contact'] as $slug) {
            $this->putJson('/api/admin/pages/'.$slug,$this->pageData())->assertOk();
            $this->getJson('/api/pages/'.$slug)->assertOk()->assertJsonPath('data.detail','Nội dung mới');
            $this->assertDatabaseHas('baiviet',['slug'=>$slug,'post_type'=>'page','created_by'=>1]);
        }
        $this->putJson('/api/admin/pages/about',array_replace($this->pageData(),['detail'=>'Đã sửa']))->assertOk();
        $this->assertDatabaseCount('baiviet',3);
        $this->getJson('/api/pages/about')->assertJsonPath('data.detail','Đã sửa');
        $this->getJson('/api/admin/posts')->assertJsonCount(0,'data');
    }

    public function test_drafts_hidden_and_deleted_pages_are_not_public_even_through_legacy_api(): void
    {
        $this->getJson('/api/pages/about')->assertNotFound();
        foreach([0,2] as $status) {
            DB::table('baiviet')->updateOrInsert(['slug'=>'about'],array_replace($this->pageData(),['post_type'=>'page','status'=>$status]));
            $this->getJson('/api/pages/about')->assertNotFound();
            $this->getJson('/api/baiviets')->assertOk()->assertExactJson([]);
        }
        DB::table('baiviet')->where('slug','about')->update(['status'=>1,'deleted_at'=>now()]);
        $this->getJson('/api/pages/about')->assertNotFound();
    }

    public function test_public_pages_and_settings_do_not_require_login(): void
    {
        DB::table('baiviet')->insert($this->pageData()+['slug'=>'about','post_type'=>'page']);
        $this->getJson('/api/pages/about')->assertOk()->assertJsonPath('data.title','Giới thiệu');
        $this->getJson('/api/site-settings')->assertOk()->assertJsonPath('data.site_name','HH Shop');
    }

    public function test_settings_persist_and_validate_without_arbitrary_keys(): void
    {
        $this->admin();
        $body=['site_name'=>'Cửa hàng thử nghiệm','contact_email'=>'shop@example.com','contact_phone'=>'+84 901 234 567','address'=>'Địa chỉ thử','footer_text'=>'Chân trang mới','meta_description'=>'Mô tả mới','password'=>'secret'];
        $this->putJson('/api/admin/site-settings',$body)->assertOk()->assertJsonMissingPath('data.password');
        $this->getJson('/api/site-settings')->assertJsonPath('data.site_name',$body['site_name'])->assertJsonPath('data.address',$body['address']);
        $this->putJson('/api/admin/site-settings',['site_name'=>'Tên mới'])->assertOk();
        $this->assertDatabaseCount('site_settings',1);
        $this->putJson('/api/admin/site-settings',['site_name'=>''])->assertUnprocessable();
        $this->putJson('/api/admin/site-settings',['site_name'=>'Shop','contact_email'=>'invalid'])->assertUnprocessable();
        $this->putJson('/api/admin/site-settings',['site_name'=>'Shop','contact_phone'=>'javascript:alert(1)'])->assertUnprocessable();
    }

    public function test_page_validation_and_post_slug_conflicts(): void
    {
        $this->admin();
        $this->putJson('/api/admin/pages/unknown',$this->pageData())->assertNotFound();
        $this->putJson('/api/admin/pages/about',array_replace($this->pageData(),['detail'=>'']))->assertUnprocessable();
        $this->putJson('/api/admin/pages/about',array_replace($this->pageData(),['status'=>9]))->assertUnprocessable();
        DB::table('baiviet')->insert($this->pageData()+['slug'=>'about','post_type'=>'post']);
        $this->putJson('/api/admin/pages/about',$this->pageData())->assertStatus(409);
        $this->getJson('/api/pages/about')->assertNotFound();
    }
}
