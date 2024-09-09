<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
    
    // 未ログインのユーザーは会員側の会社概要ページにアクセスできる
    public function test_guest_can_access_company_index_page():void
    {
        $company = Company::factory()->create();
        $response = $this->get(route('company.index'));
        $response->assertStatus(200);
    }

    // ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
    public function test_regular_can_access_company_index_page():void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('company.index'));
        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会社概要ページにアクセスできない
    public function test_admin_cannot_access_company_index_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin2@gmail.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin,'admin')->get(route(('company.index')));
        $response->assertRedirect(route('admin.home'));
    }
}
