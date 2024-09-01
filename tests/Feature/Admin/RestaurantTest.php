<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\Restaurant;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function  test_guest_cannot_access_admin_users_index()
    {
        $response = $this->get(route('admin.restaurants.index'));

        $response->assertStatus(404);

    }

    //  ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
     public  function test_regular_cannot_access_admin_user_index()
     {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));

        $response->assertStatus(200);
     }
      
 // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
 public function test_regular_admin_can_access_admin_user_index(): void
 {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();

     $restaurant = Restaurant::factory()->create();

     // 管理者としてログイン
    $this->actingAs($admin);

    // 会員の詳細ページにアクセス
    $response = $this->get(route('admin.restaurants.index', ['restaurant' => $restaurant]));

    // リダイレクトされることを確認
    $response->assertStatus(200);
}



}
