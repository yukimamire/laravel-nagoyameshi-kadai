<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;




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
// Auth::routes(['verify' => true]);
// Route::get('/home', function () {
//     return view('home'); // 認証済みユーザーのホームページビュー
// })->middleware(['auth', 'verified'])->name('home');

// Route::get('/', function () {
//     return view('welcome');
// });


// admin以外アクセス可能
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/',[HomeController::class,'index'])->name('home');

    // レストラン
    Route::resource('restaurants',RestaurantController::class)->only(['index']);
    

    // ユーザー情報
 Route::group(['middleware' => ['auth','verified']], function () {
    Route::resource('user',UserController::class)->only(['index','edit','update']);

 });
   
});

// routeの分割
// failパス
require __DIR__.'/auth.php';

// prefix=>admin admin配下(パス)  as=>名前付きルート auth:admin->auth.php定義されたadminガードで認証
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin']], function () {
    // HOME
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    // User
    Route::resource('users',Admin\UserController::class)->only(['index','show']);
    //レストラン管理
    Route::resource('restaurants',Admin\RestaurantController::class);

     // カテゴリ
    Route::resource('categories',Admin\CategoryController::class)->only(['index','store','update','destroy']);
    
    // 会社概要
    Route::resource('company',Admin\CompanyController::class)->only(['index','edit','update']);
    //   利用規約
    Route::resource('terms',Admin\TermController::class)->only(['index','edit','update']);
});



