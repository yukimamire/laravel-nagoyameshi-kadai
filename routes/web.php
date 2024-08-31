<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeController;
// use App\Http\Controllers\Admin\RestaurantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



require __DIR__.'/auth.php';

// Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
//     Route::get('home', [Admin\HomeController::class, 'index'])->name('home');

//         // 会員一覧ページ
//         Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
//         // 会員詳細ページ
//         Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    
//     });

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    
   
    Route::resource('users',UserController::class)->only(['index','show']);
});

// Route::get('admin/restaurants/index',[Admin\RestaurantController::class,'index'])->name('admin.restaurant.index');
// Route::get('admin/restaurants/create',[Admin\RestaurantController::class,'create'])->name('admin.restaurant.create');
// Route::get('admin/restaurants/store',[Admin\RestaurantController::class,'store'])->name('admin.restaurant.store');