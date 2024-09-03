<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
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
        // ダミーデータ3つを作り、IDを定義
        $categories = Category::factory()->count(3)->create();
        $category_ids = $categories->pluck('id')->toArray();

        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('admin.restaurants.store');

        $response->assertStatus(404);
  }
    
    // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
    public function test_regular_cannot_access_admin_registration_restaurants_store():void
{
    $categories = Category::factory()->count(3)->create();
     $category_ids = $categories->pluck('id')->toArray();

    $user = User::factory()->create();
    //  送信するレストランデータを定義し、category_ids パラメータを追加
    $restaurant_data = [
        'name' => 'テスト',
        'description' => 'テスト',
        'lowest_price' => 1000,
         'highest_price' =>	5000,
         'postal_code' => '0000000',
         'address' =>     'テスト',
         'opening_time' =>	'10:00',
         'closing_time' =>	'20:00',
         'seating_capacity' =>	50,
        'category_ids' => $category_ids
     ];

     $response = $this->actingAs($user)->post(route('admin.restaurants.store'),$restaurant_data);

     $response->assertRedirect(route('admin.login'));
}
   
   // ログイン済みの管理者は店舗を登録できる
   public function test_regular_admin_can_registration_restaurants_store():void
   {
     // ダミーデータ3つを作り、IDを定義
     $categories = Category::factory()->count(3)->create();
     $category_ids = $categories->pluck('id')->toArray();

     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();

     //  送信するレストランデータを定義し、category_ids パラメータを追加
     $restaurant_data = [
        'name' => 'テスト',
        'description' => 'テスト',
        'lowest_price' => 1000,
         'highest_price' =>	5000,
         'postal_code' => '0000000',
         'address' =>     'テスト',
         'opening_time' =>	'10:00',
         'closing_time' =>	'20:00',
         'seating_capacity' =>	50,
        'category_ids' => $category_ids
     ];

    // レストランを登録するリクエストを管理者として送信
     $response = $this->actingAs($admin,'admin')->post(route('admin.restaurants.store'),$restaurant_data);

     //レスポンスとレストランデータが正しく登録されたかを検証
    $response->assertStatus(302); // 成功
    unset($restaurant_data['category_ids']); // category_ids を削除
    $this->assertDatabaseHas('restaurants', $restaurant_data); // restaurantsテーブルを確認

     //category_restaurant テーブルにデータが存在することを検証
     foreach($category_ids as $category_id) {
        $this->assertDatabaseHas('category_restaurant',[
            'restaurant_id' => Restaurant::first()->id,
            'category_id' => $category_id,
        ]);
     }  
    }

    //未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
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

    // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
    public function test_regular_admin_can_access_admin_restaurant_edit(): void
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $restaurant = Restaurant::factory()->create();
        
        $this->actingAs($admin,'admin');

        $response = $this->get(route('admin.restaurants.edit',['restaurant' => $restaurant]));
        
        $response->assertStatus(200);
    }

    // 未ログインのユーザーは店舗ぺージを更新できない
    public function test_guest_cannot_access_update_admin_restaurant_page():void
    {
       // ダミーデータ3つを作り、IDを定義
       $categories = Category::factory()->count(3)->create();
       $category_ids = $categories->pluck('id')->toArray();

       $user = User::factory()->create();
       
       $response = $this->actingAs($user)->get('admin.restaurants.update');

       $response->assertStatus(404);
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
     
    // ダミーデータ3つを作り、IDを定義
    $categories = Category::factory()->count(3)->create();
    $category_ids = $categories->pluck('id')->toArray();
  

     $old_restaurant = Restaurant::factory()->create();
     $new_restaurant = [
        'name' => 'テスト',
        'description' => 'テスト',
        'lowest_price' => 1000,
         'highest_price' =>	5000,
         'postal_code' => '0000000',
         'address' =>     'テスト',
         'opening_time' =>	'10:00',
         'closing_time' =>	'20:00',
         'seating_capacity' =>	50,
         'category_ids' => $category_ids,
     ];

    // レストランを登録するリクエストを管理者として送信
     $response = $this->actingAs($admin,'admin')->patch(route('admin.restaurants.update',$old_restaurant),$new_restaurant);
     unset($new_restaurant['category_ids']);

     $this->assertDatabaseHas('restaurants', $new_restaurant); 

     
        // restaurants テーブルの中で最新のIDをを取得
        $restaurant = Restaurant::latest('id')->first();

        // category_restaurant テーブルにカテゴリが登録されていることを確認
        foreach ($category_ids as $category_id) {
            $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
        }
         $response->assertRedirect(route('admin.restaurants.show', $old_restaurant));
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