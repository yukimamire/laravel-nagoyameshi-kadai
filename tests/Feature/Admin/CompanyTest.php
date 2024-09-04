<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controllers\Admin\CompanyController;



class CompanyTest extends TestCase
{
    use RefreshDatabase;

    // index(会社概要ページ)
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_profile_page():void   
    {
    
        $response = $this->get(route('admin.company.index'));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_regular_cannot_access_admin_company_profile_page():void 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.index'));

        $response->assertStatus(302);
    }

    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_can_access_admin_company_profile_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();
        $response = $this->actingAs($admin,'admin')->get(route('admin.company.index'));

        $response->assertStatus(200);
    }

     // edit(会社概要編集ページ)
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_profile_edit_page():void   
    {
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.edit',$company));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_regular_cannot_access_admin_company_profile_edit_page():void 
    {
        $user = User::factory()->create();

        $company = Company::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.company.edit',$company));

        $response->assertStatus(302);
    }

    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_can_access_admin_company_profile_edit_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $company = Company::factory()->create();
        $response = $this->actingAs($admin,'admin')->get(route('admin.company.edit',$company));

        $response->assertStatus(200);
    }

     // update(会社概要編集ページ)
    // 未ログインのユーザーは管理者側の会社概要を更新できない
    public function test_guest_cannot_access_admin_company_profile_update():void
    {
        $old_company = Company::factory()->create();
        $new_company = [
            'name' => 'テスト',
                'postal_code' => '0000000',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
        ];
        $response = $this->patch(route('admin.company.update',$old_company),$new_company);
        
        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の会社概要を更新できない
    public function test_regular_cannot_access_admin_company_profile_update():void 
    {
        $user = User::factory()->create();

        $old_company = Company::factory()->create();
        $new_company = [
                'name' => 'テスト',
                'postal_code' => '0000000',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
        ];
        $response = $this->actingAs($user)->patch(route('admin.company.update',$old_company),$new_company);

        $response->assertRedirect(route('admin.login'));
    }

    // / ログイン済みの管理者は管理者側の会社概要を更新できる
    public function test_admin_can_access_admin_company_profile_update():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_company = Company::factory()->create();
        $new_company = [
            'name' => 'テスト',
            'postal_code' => '0000000',
            'address' => 'テスト',
            'representative' => 'テスト',
            'establishment_date' => 'テスト',
            'capital' => 'テスト',
            'business' => 'テスト',
            'number_of_employees' => 'テスト',
    ];

     $response = $this->patch(route('admin.company.update', $old_company), $new_company);

      unset($new_company['id'],$new_company['id']);
      $this->assertDatabaseHas('companies', $new_company);

      $restaurant = Company::latest('id')->first();

        $response->assertStatus(302);
    }

    }
