<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\RestaurantController;


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

// Route::get('/', function () {
//     return view('welcome');
// });



require __DIR__.'/auth.php';


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::resource('users',UserController::class)->only(['index','show']);

    // Route::resource('restaurants',Admin\RestaurantController::class);
});


Route::group(['middleware' => 'auth:admin'], function () {
    Route::controller(RestaurantController::class)->group(function () {
        Route::get('admin/restaurants/index', 'index')->name('admin.restaurants.index');
        Route::get('admin/restaurants/show/{restaurant}','show')->name('admin.restaurants.show');
        Route::get('admin/restaurants/create', 'create')->name('admin.restaurants.create');
        Route::post('admin/restaurants','store')->name('admin.restaurants.store');
        Route::get('admin/restaurants/edit/{restaurant}','edit')->name('admin.restaurants.edit');
        Route::delete('admin/restaurants/destroy/{restaurant}','destroy')->name('admin.restaurants.destroy');
        Route::patch('admin/restaurants/update{restaurant}','update')->name('admin.restaurants.update');
    });
});
