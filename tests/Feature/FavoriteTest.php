<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    // 未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
    public function test_guest_cannot_access_favorite_index_page():void
    {
        $response = $this->get(route('favorites.index'));
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
    public function test_regular_cannot_access_favorite_index_page():void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('favorites.index'));
        $response->assertRedirect(route('subscription.create'));

    }

    // ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
    public function test_premium_user_can_access_favorite_index_page():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

        $response =$this->actingAs($user)->get(route('favorites.index'));
        $response->assertStatus(200);
}

    // ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない
    public function test_admin_cannot_access_favorite_index_page():void
    {
        $admin = new Admin();
        $admin->email = 'admin2@gmail.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin')->get(route('favorites.index'));

        $response->assertRedirect(route('admin.home'));
    }

    // 未ログインのユーザーはお気に入りに追加できない
    public function test_guest_cannot_add_to_favorite():void
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->post(route('favorites.store', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はお気に入りに追加できない
    public function test_regular_cannot_add_to_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.store', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお気に入りに追加できる
    public function test_premium_user_can_add_to_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

        $response = $this->actingAs($user)->post(route('favorites.store',$restaurant->id));

        $this->assertDatabaseHas('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertStatus(302);
    }

    // ログイン済みの管理者はお気に入りに追加できない
    public function test_admin_cannot_add_to_favorite():void
    {
        $admin = new Admin();
        $admin->email = 'admin2@gmail.com';
        $admin->password = Hash::make('password');
        $admin->save();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin')->post(route('favorites.store', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('admin.home'));
    }

    // 未ログインのユーザーはお気に入りを解除できない
    public function test_guest_cannot_release_to_favorite():void
    {
        $restaurant = Restaurant::factory()->create();

        $response = $this->delete(route('favorites.destroy', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員はお気に入りを解除できない
    public function test_regular_cannot_release_to_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお気に入りを解除できる
    public function test_premium_user_can_release_to_favorite():void
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

        $response = $this->actingAs($user)->delete(route('favorites.destroy',$restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertStatus(302);
    }

    // ログイン済みの管理者はお気に入りを解除できない
    public function test_admin_cannot_release_to_favorite():void
    {
        $admin = new Admin();
        $admin->email = 'admin2@gmail.com';
        $admin->password = Hash::make('password');
        $admin->save();
        $restaurant = Restaurant::factory()->create();

        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($admin,'admin')->delete(route('favorites.destroy', $restaurant->id));

        $this->assertDatabaseMissing('restaurant_user', ['restaurant_id' => $restaurant->id]);
        $response->assertRedirect(route('admin.home'));
    }
}
