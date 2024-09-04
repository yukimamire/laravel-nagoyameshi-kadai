<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;



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

Route::get('/', function () {
    return view('welcome');
});



require __DIR__.'/auth.php';


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


