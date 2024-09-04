<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\Admin;
use App\Models\Term;
use App\Http\Controllers\Admin\CompanyController;


class TermTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // index(利用規約ページ)
    // 未ログインのユーザーは管理者側の利用規約ページにアクセスできない
    public function test_guest_cannot_access_admin_term_profile_page():void   
    {
    
        $response = $this->get(route('admin.terms.index'));

        $response->assertRedirect(route('admin.login'));
    }
    
    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_regular_cannot_access_admin_term_profile_page():void 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.index'));

        $response->assertStatus(302);
    }

    // ログイン済みの管理者は管理者側の利用規約ページにアクセスできる
    public function test_admin_can_access_admin_term_profile_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $term = Term::factory()->create();
        $response = $this->actingAs($admin,'admin')->get(route('admin.terms.index'));

        $response->assertStatus(200);

    }
    // edit(会社概要編集ページ)
    // 未ログインのユーザーは管理者側の利用規約編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_profile_term_page():void   
    {
        $term = Term::factory()->create();

        $response = $this->get(route('admin.terms.edit',$term));

        $response->assertRedirect(route('admin.login'));
    }

    // ログイン済みの一般ユーザーは管理者側の利用規約ページにアクセスできない
    public function test_regular_cannot_access_admin_term_profile_edit_page():void 
    {
        $term = Term::factory()->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.terms.edit',$term));

        $response->assertStatus(302);
}

   // ログイン済みの管理者は管理者側の利用規約編集ページにアクセスできる
   public function test_admin_can_access_admin_company_profile_edit_page():void
   {
       $admin = new Admin();
       $admin->email = 'admin@example.com';
       $admin->password = Hash::make('nagoyameshi');
       $admin->save();

       $term = Term::factory()->create();
       $response = $this->actingAs($admin,'admin')->get(route('admin.terms.edit',$term));

       $response->assertStatus(200);
   }

   // update(利用規約ページ)
    // 未ログインのユーザーは管理者側の利用規約ページを更新できない
    public function test_guest_cannot_access_admin_term_profile_update():void   
    {
        $old_term = term::factory()->create();
        $new_term = [
            'content' => 'テスト'
        ];

        $response = $this->patch(route('admin.terms.update',$old_term),$new_term);

        $response->assertRedirect(route('admin.login'));

    }

    // ログイン済みの一般ユーザーは管理者側の利用規約を更新できない
    public function test_regular_cannot_access_admin_term_profile_update():void 
    {
        $user = User::factory()->create();

        $old_term = term::factory()->create();
        $new_term = [
            'content' => 'テスト'
        ];

        $response = $this->actingAs($user)->patch(route('admin.terms.update',$old_term),$new_term);

        $response->assertRedirect(route('admin.login'));
    }
    
   // ログイン済みの管理者は管理者側の利用規約編集ページを更新できる
   public function test_admin_can_access_admin_company_profile_update():void
   {
       $admin = new Admin();
       $admin->email = 'admin@example.com';
       $admin->password = Hash::make('nagoyameshi');
       $admin->save();

       $old_term = Term::factory()->create();
       $new_term = [
          'content' => 'テスト'
       ];

       $response = $this->actingAs($admin,'admin')->patch(route('admin.terms.update',$old_term),$new_term);

       unset($new_term['id'],$new_term['id']);
       $this->assertDatabaseHas('terms',$new_term);

       $term = Term::latest('id')->first();

       $response->assertStatus(302);
   }

}

