<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\Restaurant;
use App\Providers\RouteServiceProvider;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;


    // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない
    public function  test_guest_cannot_access_admin_users_index(): void
    {
        $response = $this->get(route('admin.restaurants.index'));

        $response->assertRedirect(route('admin.login'));
        

    }

    //  ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない
     public  function test_regular_cannot_access_admin_restaurant_index():void
     {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.restaurants.index'));

        $response->assertStatus(302);
     }
        
    // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
    public function test_regular_admin_can_access_admin_restaurant_index(): void
    {
            $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get(route('admin.restaurants.index'));
        
        $response->assertStatus(200);
}

    // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurant_show(): void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
  
    }

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_regular_cannot_access_admin_restaurants_show():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.restaurants.show');

        $response->assertStatus(404);
    }

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_regular_admin_can_access_admin_restaurant_show(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.restaurants.show',['restaurant' => $restaurant]));
        
        $response->assertStatus(200);
    }

    //   未ログインのユーザーは店舗を登録できない
    public function test_guest_cannot_registration_restaurants_store():void
    {
        $response = $this->get("/admin/users/1");
   
          $response->assertRedirect('/admin/login');
  }
    

    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_regular_cannot_access_admin_registration_restaurants_store():void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('admin.restaurants.1');

    $response->assertStatus(404);
}
   
   // ログイン済みの管理者は店舗を登録できる
   public function test_regular_admin_can_registration_restaurants_store():void
   {
     $admin = new Admin();

       $admin->email = 'admin@example.com';
       $admin->password = Hash::make('nagoyameshi');
       $admin->save();

       $restaurant = Restaurant::factory()->create();

       $response = $this->actingAs($admin,'admin');
       $response = $this->get(route('admin.restaurants.show',['restaurant' => $restaurant]));

       $response->assertStatus(200);
       }

    //    未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_guest_cannot_access_admin_restaurant_edit():void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
    }
    
    // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
    public function test_regular_cannot_access_admin_restaurant_edit():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.restaurants.edit');

        $response->assertStatus(404);

    }

    // 未ログインのユーザーは店舗ぺージを更新できない
    public function test_guest_cannot_access_update_admin_restaurant_page():void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
    }

    // ログイン済みの一般ユーザーは店舗ページを更新できない
    public function test_regular_cannot_update_admin_restaurant_page():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.restaurants.update');

        $response->assertStatus(404);

     }

    // ログイン済みの管理者は店舗ページを更新できる
    public function test_regular_admin_can_registration_restaurants_update():void
   {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin');
        $response = $this->get(route('admin.restaurants.show',['restaurant' => $restaurant]));

        $response->assertStatus(200);
   }
        
        // 未ログインのユーザーは店舗ページを削除できない
        public function test_guest_cannot_access_delete_admin_restaurant_page():void
    {
        $response = $this->get("/admin/users/1");
   
        $response->assertRedirect('/admin/login');
    }

       // ログイン済みの一般ユーザーは店舗を削除できない
       public function test_regular_cannot_delete_admin_restaurant_page():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('admin.restaurants.delete');

        $response->assertStatus(404);

     }

    //  ログイン済みの管理者は店舗を削除できる
    public function test_regular_admin_can_delete_registration_restaurants_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        // $admin->save();

        $restaurant = Restaurant::factory()->create();

       $response = $this->actingAs($admin, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));

       $this->assertDatabaseMissing('restaurants', [
        'id' => $restaurant->id, // 削除されたレコードを特定するための条件を指定
    ]);

       $response->assertRedirect(route('admin.restaurants.index'));

    }

}