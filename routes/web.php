<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LogoutController;
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
Route::get('/', [HomeController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::group(['middleware' => ['auth']], function() {
    /**
    * Logout Route
    */
    Route::get('/logout', [LogoutController::class, 'perform'])->name('logout.perform');
 });

Route::get('/redirect', [HomeController::class, 'redirect']);

Route::get('/view_catagory', [AdminController::class, 'view_catagory'])->middleware('auth');

Route::post('/add_catagory', [AdminController::class, 'add_catagory'])->middleware('auth');

Route::get('/delete_catagory/{id}', [AdminController::class, 'delete_catagory'])->middleware('auth');

Route::get('/view_product', [AdminController::class, 'view_product'])->middleware('auth');

Route::post('/add_product', [AdminController::class, 'add_product'])->middleware('auth');

Route::get('/show_product', [AdminController::class, 'show_product'])->middleware('auth');

Route::get('/delete_product/{id}', [AdminController::class, 'delete_product'])->middleware('auth');

Route::get('/update_product/{id}', [AdminController::class, 'update_product'])->middleware('auth');

Route::post('/update_product_confirm/{id}', [AdminController::class, 'update_product_confirm'])->middleware('auth');

Route::get('/product_details/{id}', [HomeController::class, 'product_details']);

Route::post('/add_cart/{id}', [HomeController::class, 'add_cart'])->middleware('auth');

Route::get('/show_cart', [HomeController::class, 'show_cart'])->middleware('auth');

Route::get('/remove_cart/{id}', [HomeController::class, 'remove_cart'])->middleware('auth');

Route::get('/cash_order', [HomeController::class, 'cash_order'])->middleware('auth');

Route::get('/stripe/{totalprice}', [HomeController::class, 'stripe'])->middleware('auth');

Route::post('stripe/{totalprice}', [HomeController::class,'stripePost'])->name('stripe.post');