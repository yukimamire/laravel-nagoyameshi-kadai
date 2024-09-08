<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SubscriptionTest extends TestCase
{

    use RefreshDatabase;
// create (有料プラン登録ぺージ)
// 未ログインのユーザーは有料プラン登録ページにアクセスできない
public function test_guest_cannot_access_premium_plan_page():void
{
    $response = $this->get(route('subscription.create'));

    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員は有料プラン登録ページにアクセスできる
public function test_regular_can_access_premium_plan_page():void
{
    $user = User::factory()->create();
 
    $response = $this->actingAs($user)->get(route('subscription.create'));

    $response->assertStatus(200);
}

// ログイン済みの有料会員は有料プラン登録ページにアクセスできない
public function test_regular_in_membership_user_cannot_access_premium_plan_page():void
{ 
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');
 
    $response = $this->actingAs($user)->get(route('subscription.create'));
 
    $response->assertRedirect(route('subscription.edit'));
}

// ログイン済みの管理者は有料プラン登録ページにアクセスできない
public function test_admin_cannot_access_premium_plan_page():void
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin,'admin')->get(route('subscription.create'));
    $response->assertRedirect(route('admin.home'));
}

// store(有料プラン登録機能)
// 未ログインのユーザーは有料プランに登録できない
public function test_guest_cannot_premium_plan_registration():void
{
    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];

    $response = $this->post(route('subscription.store'),$request_parameter);

    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員は有料プランに登録できる
public function test_regular_can_register_premium_plan():void
{
    $user = User::factory()->create();

    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];

    $response = $this->actingAs($user)->post(route('subscription.store'),$request_parameter);

    $response->assertRedirect(route('home'));

    $user->refresh();
    $this->assertTrue($user->subscribed('premium_plan'));

}

// ログイン済みの有料会員は有料プランに登録できない
public function test_regular_in_membership_user_cannot_register_premium_plan():void
{
    $user = User::factory()->create();

    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];

    $response = $this->actingAs($user)->post(route('subscription.store'),$request_parameter);

    $response->assertRedirect(route('home'));

}

// ログイン済みの管理者は有料プランに登録できない
public function test_admin_cannot_register_premium_plan_page():void
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
    ];

    $response = $this->actingAs($admin,'admin')->post(route('subscription.store'),$request_parameter);
    $response->assertRedirect(route('admin.home'));
}

// 未ログインのユーザーはお支払い方法編集ページにアクセスできない
public function test_guest_cannot_access_payment_method_edit_page():void
{
    $response = $this->get(route('subscription.edit'));

    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員はお支払い方法編集ページにアクセスできない
public function test_regular_can_access_payment_method_edit_page():void
{
    $user = User::factory()->create();
 
    $response = $this->actingAs($user)->get(route('subscription.edit'));

    $response->assertRedirect(route('subscription.create'));
}

// ログイン済みの有料会員はお支払い方法編集ページにアクセスできる
public function test_regular_in_membership_user_cannot_access_payment_method_edit_page():void
{ 
    $user = User::factory()->create();

    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');
 
    $response = $this->actingAs($user)->get(route('subscription.edit'));
 
    $response->assertStatus(200);
}

// ログイン済みの管理者は有料プラン登録ページにアクセスできない
public function test_admin_cannot_access_payment_method_edit_page():void
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin,'admin')->get(route('subscription.edit'));
    $response->assertRedirect(route('admin.home'));
}

// 未ログインのユーザーはお支払い方法を更新できない
public function test_guest_cannot_update_payment_method():void
{
    $request_parameter = [
        'paymentMethodId' => 'pm_card_mastercard'
    ];

    $response = $this->patch(route('subscription.update'),$request_parameter);

    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員はお支払い方法を更新できない
public function test_regular_cannot_update_payment_method():void
{
    $user = USer::factory()->create();
    $request_parameter = [
        'paymentMethodId' => 'pm_card_mastercard'
    ];

    $response = $this->actingAs($user)->patch(route('subscription.update'),$request_parameter);

    $response->assertRedirect(route('subscription.create'));

}

// ログイン済みの有料会員はお支払い方法を更新できる
public function test_regular_can_update_payment_method():void
{
    $user = User::factory()->create();

    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');
 
    $original_payment_method_id = $user->defaultPaymentMethod()->id;

    $request_parameter = [
        'paymentMethodId' => 'pm_card_mastercard'
    ];

    $response = $this->actingAs($user)->patch(route('subscription.update'),$request_parameter);

    $response->assertRedirect(route('home'));

    
    $user->refresh();
    $this->assertNotEquals($original_payment_method_id, $user->defaultPaymentMethod()->id);

}

// 未ログインのユーザーは有料プラン解約ページにアクセスできない
public function test_guest_cannot_access_paid_plan_cancellation_page():void
{
    $response = $this->get(route('subscription.cancel'));

    $response->assertRedirect(route('login'));
}

// ログイン済みの無料会員は有料プラン解約ページにアクセスできない
public function test_regular_can_access_paid_plan_cancellation_page():void
{
    $user = User::factory()->create();
 
    $response = $this->actingAs($user)->get(route('subscription.cancel'));

    $response->assertRedirect(route('subscription.create'));
}

// ログイン済みの有料会員は有料プラン解約ページにアクセスできる
public function test_regular_in_membership_user_cannot_access_paid_plan_cancellation_page():void
{ 
    $user = User::factory()->create();

    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');
 
    $response = $this->actingAs($user)->get(route('subscription.cancel'));
 
    $response->assertStatus(200);
}

// ログイン済みの管理者は有料プラン解約ページにアクセスできない
public function test_admin_cannot_access_paid_plan_cancellation_page():void
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin,'admin')->get(route('subscription.cancel'));
    $response->assertRedirect(route('admin.home'));
}

// 未ログインのユーザーは有料プランを解約できない
public function test_guest_paid_plan_cannot_cancellation():void
{
    $response = $this->delete(route('subscription.destroy'));

    $response->assertRedirect(route('login'));

}

// ログイン済みの無料会員は有料プランを解約できない
public function test_regular_paid_plan_cannot_cancellation():void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('subscription.destroy'));
    $response->assertRedirect(route('subscription.create'));

}

// ログイン済みの有料会員は有料プランを解約できる
public function test_regular_paid_plan_can_cancellation():void
{
    $user = User::factory()->create();

    $user->newSubscription('premium_plan', 'price_1PvxdO04OnjWkVQEu1QzznZB')->create('pm_card_visa');

    $response = $this->actingAs($user)->delete(route('subscription.destroy'));

    $response->assertRedirect(route('home'));

    $user->refresh();
    $this->assertFalse($user->subscribed('premium_plan'));
}

// ログイン済みの管理者は有料プランを解約できない
public function test_admin_paid_plan_cannot_cancellation():void
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $response = $this->actingAs($admin,'admin')->delete(route('subscription.destroy'));
    $response->assertRedirect(route('admin.home'));


    }
}