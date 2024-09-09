<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Support\Facades\Hash;

class ReservationTest extends TestCase
{
    use RefreshDatabase;
   
// 未ログインのユーザーは会員側の予約一覧ページにアクセスできない
public function test_guest_cannot_access_reservation_index():void
{
    $response = $this->get(route('reservations.index'));
    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員は会員側の予約一覧ページにアクセスできない
public function test_regular_cannot_access_reservation_index():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($user)->get(route('reservations.index'));
    $response->assertRedirect(route('subscription.create'));
}

// ログイン済みの有料会員は会員側の予約一覧ページにアクセスできる
public function test_premium_user_can_access_reservation_index():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

    $response = $this->actingAs($user)->get(route('reservations.index'));
    $response->assertStatus(200);
}

// ログイン済みの管理者は会員側の予約一覧ページにアクセスできない
public function test_admin_cannot_access_reservation_index():void
{
    $admin = new Admin();
    $admin->email = 'admin2@gmail.com';
    $admin->password = Hash::make('password');
    $admin->save();

    $response = $this->actingAs($admin,'admin')->get(route('reservations.index'));

    $response->assertRedirect(route('admin.home'));
}

// 未ログインのユーザーは会員側の予約ページにアクセスできない
public function test_guest_cannot_access_reservation_create():void
{
    $restaurant = Restaurant::factory()->create();
    $response = $this->get(route('restaurants.reservations.create',$restaurant));
    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員は会員側の予約ページにアクセスできない
public function test_regular_cannot_access_reservation_create():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($user)->get(route('restaurants.reservations.create',$restaurant));
    $response->assertRedirect(route('subscription.create'));
}

// ログイン済みの有料会員は会員側の予約ページにアクセスできる
public function test_premium_user_can_access_reservation_create():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

    $response = $this->actingAs($user)->get(route('restaurants.reservations.create',$restaurant));
    $response->assertStatus(200);
}

// ログイン済みの管理者は会員側の予約ページにアクセスできない
public function test_admin_cannot_access_reservation_create():void
{
    $admin = new Admin();
    $admin->email = 'admin2@gmail.com';
    $admin->password = Hash::make('password');
    $admin->save();

    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($admin,'admin')->get(route('restaurants.reservations.create',$restaurant));

    $response->assertRedirect(route('admin.home'));
}

// 未ログインのユーザーは予約できない
public function test_guest_cannot_reservation():void
{
    $restaurant = Restaurant::factory()->create();
    $reservationData = [
        'reserved_datetime' => now(),
        'number_of_people' => 4,
        'restaurant_id' => 11,
    ];

    $response = $this->post(route('restaurants.reservations.store',$restaurant),$reservationData);
    $response->assertRedirect('login');
}

// ログイン済みの無料会員は予約できない
public function test_regular_cannot_reservation():void
{
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $reservationData = [
        'reserved_datetime' => now(),
        'number_of_people' => 4,
        'restaurant_id' => 11,
    ];
    $response = $this->actingAs($user)->post(route('restaurants.reservations.store',$restaurant),$reservationData);

    $response->assertRedirect(route('subscription.create'));
}

// ログイン済みの有料会員は予約できる
public function test_premium_user_can_reservation():void
{
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

    $restaurant = Restaurant::factory()->create();

        $reservation_data = [
            'reservation_date' => '2024-01-01',
            'reservation_time' => '00:00',
            'number_of_people' => 10
        ];

        $response = $this->actingAs($user)->post(route('restaurants.reservations.store', $restaurant), $reservation_data);

        $this->assertDatabaseHas('reservations', ['reserved_datetime' => '2024-01-01 00:00', 'number_of_people' => 10]);
        $response->assertRedirect(route('reservations.index',$restaurant));
    }

// ログイン済みの管理者は予約できない
public function test_admin_cannot_reservation():void
{
    $admin = new Admin();
    $admin->email = 'admin2@gmail.com';
    $admin->password = Hash::make('password');
    $admin->save();

    $restaurant = Restaurant::factory()->create();

    $reservationData = [
        'reserved_datetime' => now(),
        'number_of_people' => 4,
        'restaurant_id' => 11,
    ];

    $response = $this->actingAs($admin,'admin')->post(route('restaurants.reservations.store',$restaurant),$reservationData);

    $response->assertRedirect(route('admin.home'));
}

// 未ログインのユーザーは予約をキャンセルできない
public function test_guest_cannot_reservation_cancel():void
{
    
    $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('login'));
    }

// ログイン済みの無料会員は予約をキャンセルできない
public function test_regular_cannot_reservation_cancel():void
{
    
    $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create();

        $reservation = Reservation::factory()->create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $response->assertRedirect(route('subscription.create'));
    }

// ログイン済みの有料会員は他人の予約をキャンセルできない
public function test_premium_user_cannot_cancel_others_reservation()
 {
     $user = User::factory()->create();
     $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

     $other_user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
 
    $other_user_restaurant_date = Reservation::factory()->create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $other_user->id
    ]);
 
    $response = $this->actingAs($user)->delete(route('reservations.destroy', $other_user_restaurant_date));
 
    $this->assertDatabaseHas('reservations', ['id' => $other_user_restaurant_date->id]);
    $response->assertRedirect(route('reservations.index'));
     }

//  ログイン済みの有料会員は自身の予約をキャンセルできる
public function test_premium_user_can_cancel_reservation()
{
    $user =User::factory()->create();
    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

    $restaurant = Restaurant::factory()->create();
    $reservation = Reservation::factory()->create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id
    ]);

    $response = $this->actingAs($user)->delete(route('reservations.destroy',$reservation));
    $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    $response->assertRedirect(route('reservations.index'));
}

// ログイン済みの管理者は予約をキャンセルできない
public function test_admin_cannot_reservation_cancel():void
{
    $admin = new Admin();
    $admin->email = 'admin2@gmail.com';
    $admin->password = Hash::make('password');
    $admin->save();

    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $reservation = Reservation::factory()->create([
         'restaurant_id' => $restaurant->id,
         'user_id' => $user->id
        ]);

    $response = $this->actingAs($admin,'admin')->delete(route('reservations.destroy', $reservation));

    $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
     $response->assertRedirect(route('admin.home'));
    }
}
