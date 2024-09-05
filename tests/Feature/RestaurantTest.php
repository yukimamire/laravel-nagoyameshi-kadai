<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class RestaurantTest extends TestCase
{

     use RefreshDatabase;
// 仮テスト
// 未ログインのユーザーは会員側の店舗一覧ページにアクセスできる
public function test_guest_can_access_user_index_page():void
{
    $response = $this->get(route('restaurants.index'));
    $response->assertStatus(200);
}

public function test_regular_can_access_user_index_page():void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.index'));

    $response->assertStatus(200);

}

public function test_admin_cannot_access_user_index_page():void
{
    $admin = new Admin();
    $admin->email = 'admin2@gmail.com';
    $admin->password = Hash::make('password');
    $admin->save();

    $response = $this->actingAs($admin,'admin')->get(route('restaurants.index'));

    $response->assertRedirect(route('admin.home'));
}

}

