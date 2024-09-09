<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class HomeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // 未ログインのユーザーは管理者側のトップページにアクセスできない
    public function test_guest_cannot_access_admin_top_page():void
    {
        $response = $this->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側のトップページにアクセスでない
    public function test_regular_cannot_access_admin_top_page():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.home'));
        $response->assertRedirect(route('admin.login'));

    }

    // ログイン済みの管理者は管理者側のトップページにアクセスできる
    public function test_admin_can_access_admin_top_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin,'admin')->get(route('admin.home'));
        $response->assertStatus(200);

    }
}
