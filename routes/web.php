<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;


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


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth:admin']], function () {
    // HOME
    Route::get('home', [HomeController::class, 'index'])->name('home');
    // User
    Route::resource('users',UserController::class)->only(['index','show']);
    // // レストラン管理
    Route::resource('restaurants',Admin\RestaurantController::class);

    Route::controller(CategoryController::class)->group(function () {
        Route::get('admin/categories/index', 'index')->name('admin.categories.index');
        Route::post('admin/categories','store')->name('admin.categories.store');
        Route::delete('admin/categories/{category}','destroy')->name('admin.categories.destroy');
        Route::patch('admin/categories/update/{category}','update')->name('admin.categories.update');
});
    // //  // カテゴリ
    // Route::resource('admin.categories',CategoryController::class)
    // ->names('admin.categories')
    // ->only(['index','store','update','destroy']);
});

