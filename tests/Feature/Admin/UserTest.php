<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
//     /**
//      * A basic feature test example.
//      */
//     // public function test_example(): void
//     // {
//     //     $response = $this->get('/');

//     //     $response->assertStatus(200);
//     // }
// }
  use RefreshDatabase;

//   未ログインのユーザーは管理者側の会員一覧ページにアクセスできない
  // 未ログインのユーザーが会員一覧ページにアクセスできない
     public function test_guest_cannot_access_admin_users_index()
  {
       $response = $this->get('/admin/users');

       $response->assertRedirect('/login');
  }

  // ログイン済みの一般ユーザーが会員一覧ページにアクセスできない
      public function test_regular_user_cannot_access_admin_users_index(): void
  {
         $user = User::factory()->create();

         $response = $this->actingAs($user)->get('/admin/users');

         $response->assertStatus(403); // Forbidden
  }

  // ログイン済みの管理者が会員一覧ページにアクセスできる
       public function test_admin_can_access_admin_users_index(): void
  {
         $admin = User::factory()->admin()->create();

         $response = $this->actingAs($admin)->get('/admin/users');

         $response->assertStatus(200);
  }

  // 未ログインのユーザーが会員詳細ページにアクセスできない
       public function test_guest_cannot_access_admin_users_show(): void
  {
          $response = $this->get("/admin/users/1");
   
          $response->assertRedirect('/login');
  }

  // ログイン済みの一般ユーザーが会員詳細ページにアクセスできない
       public function test_regular_user_cannot_access_admin_users_show(): void
  {
         $user = User::factory()->create();

         $response = $this->actingAs($user)->get("/admin/users/1");

         $response->assertStatus(403); // Forbidden
  }

  // ログイン済みの管理者が会員詳細ページにアクセスできる
      public function test_admin_can_access_admin_users_show(): void
  {
      $admin = User::factory()->admin()->create();

      $response = $this->actingAs($admin)->get("/admin/users/1");

      $response->assertStatus(200);
  }
}