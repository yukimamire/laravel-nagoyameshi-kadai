<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Providers\RouteServiceProvider;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_guest_cannot_access_admin_categories_index_page():void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
    public function test_regular_cannot_access_admin_categories_index_page():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertStatus(302);
    }

    // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
    public function test_admin_can_access_admin_categories_index_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $categories = Category::factory()->create();$this->actingAs($admin,'admin');

        $response = $this->get(route('admin.categories.index'));
        
        $response->assertStatus(200);

    }
    // 未ログインのユーザーはカテゴリを登録できない
    public function test_guest_cannot_registration_admin_categories_store():void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーはカテゴリを登録できない
    public function test_regular_cannot_registration_admin_categories_store():void
    {
        $user = User::factory()->create();
         $category = Category::factory()->create();
         $data = $category->toArray();
 
         $response = $this->actingAs($user)->post(route('admin.categories.store'), $data);
  
         $this->assertDatabaseMissing('categories', $data);
         $response->assertRedirect(route('admin.login'));
    }
     // ログイン済みの管理者はカテゴリを登録できる
     public function test_admin_can_access_registration_admin_categories_store():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $category = Category::factory()->create();
     $data = $category->toArray();
 
     unset($data['created_at'], $data['updated_at']);
 
     $this->actingAs($admin);
     $response = $this->post(route('admin.categories.store'), $data);
 
     $this->assertDatabaseHas('categories', $data);
     $response->assertStatus(302);
     }
 

    // 未ログインのユーザーはカテゴリを更新できない
    public function test_guest_cannot_registration_admin_categories_update():void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーはカテゴリを更新できない
    public function test_regular_cannot_registration_admin_categories_update():void
    {
        $user = User::factory()->create();
        $old_category = Category::factory()->create();
        $new_category = Category::factory()->create();

        $new_data = $new_category->toArray();

        $response = $this->actingAs($user)->patch(route('admin.categories.update', $old_category), $new_data);

        $this->assertDatabaseMissing('categories', $new_data);
        $response->assertRedirect(route('admin.login'));
    }

    // 未ログインのユーザーはカテゴリを削除できない
    public function test_guest_cannot_registration_admin_categories_delete():void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーはカテゴリを削除できない
    public function test_regular_cannot_registration_admin_categories_delete():void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->create();

        $response = $this->actingAs($user)->get('admin.categories.delete');

        $response->assertStatus(404);

    }

    // ログイン済みの管理者はカテゴリを削除できる
    public function test_admin_can_registration_admin_categories_delete():void
    {
        $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $category = Category::factory()->create();
         $data = $category->toArray();
 
         $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category), $data);
 
         $this->assertDatabaseMissing('categories', $data);
         $response->assertStatus(302);
    }

}
