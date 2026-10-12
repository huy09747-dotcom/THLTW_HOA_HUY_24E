<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContentAdminTest extends TestCase
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
        DB::table('nguoidung')->insert([
            ['id' => 1, 'roles' => 'admin', 'status' => 1],
            ['id' => 2, 'roles' => 'user', 'status' => 1],
        ]);
    }

    private function admin(): void
    {
        Sanctum::actingAs(User::findOrFail(1));
    }

    private function postData(): array
    {
        return ['title' => 'Bai viet', 'slug' => 'bai-viet', 'detail' => 'Noi dung', 'status' => 1];
    }

    public function test_routes_require_admin(): void
    {
        foreach (['posts', 'menus'] as $kind) {
            $this->getJson("/api/admin/$kind")->assertUnauthorized();
        }
        Sanctum::actingAs(User::findOrFail(2));
        foreach (['posts', 'menus'] as $kind) {
            $this->getJson("/api/admin/$kind")->assertForbidden();
            $this->postJson("/api/admin/$kind", [])->assertForbidden();
            $this->deleteJson("/api/admin/$kind/1")->assertForbidden();
            $this->getJson("/api/admin/$kind/trash")->assertForbidden();
        }
    }

    public function test_post_crud_trash_slug_and_topic_validation(): void
    {
        $this->admin();
        DB::table('chudebaiviet')->insert(['id' => 1, 'name' => 'Cong nghe']);
        $body = $this->postData() + ['topic_id' => 1];
        $id = $this->postJson('/api/admin/posts', $body)->assertCreated()->json('data.id');
        $this->getJson('/api/admin/posts')->assertOk()->assertJsonPath('data.0.topic_name', 'Cong nghe');
        $this->putJson("/api/admin/posts/$id", $body)->assertOk();
        $this->postJson('/api/admin/posts', $body)->assertUnprocessable()->assertJsonValidationErrors('slug');
        $this->postJson('/api/admin/posts', array_replace($body, ['slug' => 'other', 'topic_id' => 999]))
            ->assertUnprocessable()->assertJsonValidationErrors('topic_id');
        $this->deleteJson("/api/admin/posts/$id")->assertOk();
        $this->getJson('/api/admin/posts')->assertJsonCount(0, 'data');
        $this->getJson('/api/admin/posts/trash')->assertJsonCount(1, 'data');
        $this->postJson('/api/admin/posts', $body)->assertUnprocessable();
        $this->patchJson("/api/admin/posts/$id/restore")->assertOk();
        $this->deleteJson("/api/admin/posts/$id/force")->assertNotFound();
        $this->deleteJson("/api/admin/posts/$id")->assertOk();
        $this->deleteJson("/api/admin/posts/$id/force")->assertOk();
        $this->assertDatabaseMissing('baiviet', ['id' => $id]);
        $this->getJson('/api/admin/posts/options')->assertOk()->assertJsonPath('topics.0.name', 'Cong nghe');
    }

    public function test_menu_crud_and_safe_links_and_target_validation(): void
    {
        $this->admin();
        $body = ['name' => 'San pham', 'link' => '/products', 'type' => 'custom', 'status' => 1];
        $id = $this->postJson('/api/admin/menus', $body)->assertCreated()->json('data.id');
        $this->putJson("/api/admin/menus/$id", array_replace($body, ['name' => 'Shop']))->assertOk();
        foreach (['javascript:alert(1)', '//evil.test', '/\\evil.test', 'data:text/html,test'] as $link) {
            $this->postJson('/api/admin/menus', array_replace($body, ['link' => $link]))
                ->assertUnprocessable()->assertJsonValidationErrors('link');
        }
        $this->postJson('/api/admin/menus', array_replace($body, ['type' => 'category', 'table_id' => 999]))
            ->assertUnprocessable()->assertJsonValidationErrors('table_id');
        DB::table('danhmuc')->insert(['id' => 1, 'name' => 'Phone']);
        $this->postJson('/api/admin/menus', array_replace($body, ['type' => 'category', 'table_id' => 1]))->assertCreated();
        $this->getJson('/api/admin/menus/options')->assertOk()->assertJsonPath('category.0.name', 'Phone');
        $this->deleteJson("/api/admin/menus/$id")->assertOk();
        $this->getJson('/api/admin/menus/trash')->assertJsonCount(1, 'data');
        $this->patchJson("/api/admin/menus/$id/restore")->assertOk();
        $this->deleteJson("/api/admin/menus/$id")->assertOk();
        $this->deleteJson("/api/admin/menus/$id/force")->assertOk();
        $this->assertDatabaseMissing('menu', ['id' => $id]);
    }

    public function test_pagination_and_pages_are_separate_from_posts(): void
    {
        $this->admin();
        for ($i = 1; $i <= 11; $i++) {
            DB::table('baiviet')->insert(array_replace($this->postData(), ['slug' => "post-$i"]));
            DB::table('menu')->insert(['name' => "Menu $i", 'link' => '/', 'type' => 'custom', 'status' => 1]);
        }
        $pageId = DB::table('baiviet')->insertGetId(array_replace($this->postData(), ['post_type' => 'page', 'slug' => 'about']));
        foreach (['posts', 'menus'] as $kind) {
            $this->getJson("/api/admin/$kind")->assertOk()->assertJsonCount(10, 'data')->assertJsonPath('meta.total', 11)->assertJsonPath('data.0.id', 11);
            $this->getJson("/api/admin/$kind?page=2")->assertJsonCount(1, 'data');
        }
        $this->deleteJson("/api/admin/posts/$pageId")->assertNotFound();
    }
}
