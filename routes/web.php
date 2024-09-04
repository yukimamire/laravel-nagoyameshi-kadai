<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\HomeController;




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


// routeの分割
// failパス
require __DIR__.'/auth.php';

// prefix=>admin admin配下(パス)  as=>名前付きルート auth:admin->auth.php定義されたadminガードで認証
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin']], function () {
    // HOME
    Route::get('home', [HomeController::class, 'index'])->name('home');
    // User
    Route::resource('users',UserController::class)->only(['index','show']);
    //レストラン管理
    Route::resource('restaurants',Admin\RestaurantController::class);

     // カテゴリ
    Route::resource('categories',Admin\CategoryController::class)->only(['index','store','update','destroy']);
    
    // 会社概要
    Route::resource('company',Admin\CompanyController::class)->only(['index','edit','update']);
    //   利用規約
    Route::resource('terms',Admin\TermController::class)->only(['index','edit','update']);
});


// admin以外アクセス可能
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/',[HomeController::class,'index'])->name('home');
});


