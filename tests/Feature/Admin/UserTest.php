<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


class UserTest extends TestCase
{
//     /**
    //  * A basic feature test example.
    //  */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
// }
     // use RefreshDatabase;

//   未ログインのユーザーは管理者側の会員一覧ページにアクセスできない
     public function test_guest_user_cannot_access_admin_users_index():void
  {
       $response = $this->get('/admin/index');

       $response->assertRedirect(route('login'));
  }

  // ログイン済みの一般ユーザーは管理者側の会員一覧ページにアクセスできない
      public function test_regular_user_cannot_access_admin_users_index(): void
  {
         $user = User::factory()->create();

         $response = $this->actingAs($user)->get('/admin/users');

         $response->assertStatus(403); // Forbidden
  }

  // ログイン済みの管理者は管理者側の会員一覧ページにアクセスできる
       public function test_regular_admin_can_access_admin_users_index(): void
  {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();

         $response = $this->actingAs($admin,'admin')->get(route('admin.users.index'));

         $response->assertStatus(200);
  }

  // 未ログインのユーザーは管理者側の会員詳細ページにアクセスできない
       public function test_guest_user_cannot_access_admin_users_show(): void
  {
          $response = $this->get("/admin/users/1");
   
          $response->assertRedirect('/admin/login');
  }

  // ログイン済みの一般ユーザーは管理者側の会員詳細ページにアクセスできない
       public function test_regular_user_cannot_access_admin_users_show(): void
  {
         $user = User::factory()->create();

         $response = $this->actingAs($user)->get("/admin/users/1");

         $response->assertStatus(403); // Forbidden
  }

  // ログイン済みの管理者は管理者側の会員詳細ページにアクセスできる
      public function test_admin_can_access_admin_users_show(): void
  {
      $admin = User::factory()->create(['is_admin' => 1]);

      $response = $this->actingAs($admin)->get("/admin/users/1");

      $response->assertStatus(200);
  }
}
