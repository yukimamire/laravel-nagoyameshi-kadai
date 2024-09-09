<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Term;
use Illuminate\Support\Facades\Hash;

class TermTest extends TestCase
{
    use RefreshDatabase;

// 未ログインのユーザーは会員側の利用規約ページにアクセスできる
public function test_guest_can_access_term_index_page():void
{
    $term = Term::factory()->create();

    $response = $this->get(route('terms.index'));
    $response->assertStatus(200);
}    

// ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
public function test_regular_can_access_term_index_page():void
{
    $term = Term::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('terms.index'));
    $response->assertStatus(200);
}

// ログイン済みの管理者は会員側の利用規約ページにアクセスできない
public function test_admin_cannot_access_term_index_page():void
{
    $admin = new Admin();
    $admin->email = 'admin2@gmail.com';
    $admin->password = Hash::make('password');
    $admin->save();

    $term = Term::factory()->create();

    $response = $this->actingAs($admin,'admin')->get(route('terms.index'));
    $response->assertRedirect(route('admin.home'));

}
}