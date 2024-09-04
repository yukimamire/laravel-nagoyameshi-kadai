<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controller\UserController;


class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    //  index（会員情報ページ）
    // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_information_page():void
    {
        $response = $this->get(route('user.index'));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_regular_can_access_user_information_page():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));

        $response->assertStatus(200);
    }
    // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    public function test_admin_cannot_access_user_information_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin,'admin')->get(route('user.index'));

        $response->assertRedirect(route('admin.home'));
    }

    // edit（会員情報編集ページ）
    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_cannot_access_user_information_edit_page():void
    {
        $user = USer::factory()->create();

        $response = $this->get(route('user.edit',$user));

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_regular_cannot_access_other_user_information_edi_page():void
    {
        $user = User::factory()->create();
        $other_user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit',$other_user));

        $response->assertRedirect(route('user.index'));

    }

    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
     public function test_regular_can_access_user_information_edit_page():void
     {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit',$user));

        $response->assertStatus(200);
     }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_user_information_edit_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $user = User::factory()->create();

        $response = $this->actingAs($admin,'admin')->get(route('user.edit',$user));

        $response->assertRedirect(route('admin.home'));
    }

    // update （会員情報更新機能）
    // 未ログインのユーザーは会員情報を更新できない
    public function test_guest_cannot_access_user_information_update():void
    {
        $user = User::factory()->create();
    
        $data = [
           'name' => 'テスト',
            'kana'=> 'テスト',
            'email'=> 'admin@gmail.com',
            'postal_code' => '0111111',
            'address' => 'テスト',
            'phone_number' => '1234567890',
            'birthday' => '1999909',
            'occupation' => 'テスト',
        ];

        $response = $this->patch(route('user.update',['user' => $user->id]),$data);

        $this->assertDatabaseMissing('users',$data);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_regular_cannot_access_other_user_information_update():void
    {
        $user = User::factory()->create();
        $old_other_user = User::factory()->create();

        $new_other_user = [
            'name' => 'テスト',
            'kana'=> 'テスト',
            'email'=> 'admin@gmail.com',
            'postal_code' => '0111111',
            'address' => 'テスト',
            'phone_number' => '1234567890',
            'birthday' => '1999909',
            'occupation' => 'テスト',
        ];

        $response = $this->actingAs($user)->patch(route('user.update',$old_other_user),$new_other_user);
        $this->assertDatabaseMissing('users',$new_other_user);

        $response->assertRedirect(route('user.index'));

    }

    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_regular_can_access_user_information_update():void
     {
        $old_user_data = User::factory()->create();

        $new_user_data = [

            'name' => 'テスト',
            'kana'=> 'テスト',
            'email'=> 'admin@gmail.com',
            'postal_code' => '0111111',
            'address' => 'テスト',
            'phone_number' => '1234567890',
            'birthday' => '20241010',
            'occupation' => 'テスト',
        ];

        $response = $this->actingAs($old_user_data)->patch(route('user.update',$old_user_data),$new_user_data);

        $this->assertDatabaseHas('users', $new_user_data);

        $response->assertRedirect(route('user.index'));
     }

     // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_user_update():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $old_user_data = User::factory()->create();
        $new_user_data = [
            'name' => 'テスト',
            'kana'=> 'テスト',
            'email'=> 'admin@gmail.com',
            'postal_code' => '0111111',
            'address' => 'テスト',
            'phone_number' => '1234567890',
            'birthday' => '20241010',
            'occupation' => 'テスト',
        ];

        $response = $this->actingAs($admin,'admin')->patch(route('user.update',$old_user_data),$new_user_data);

        $response->assertRedirect(route('admin.home'));
    }

    }
