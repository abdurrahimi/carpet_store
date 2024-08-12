<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UsersController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/deploy', function (Request $request){
    return response('Deploy bro', 200);
});

Route::get('/', function () {
    return redirect('/dashboard');
    /* return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]); */
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Karyawan
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/karyawan', [UsersController::class, 'getDataUsers'])->name('users.get');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/data', [ProductController::class, 'getDataProduct'])->name('products.get');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'delete'])->name('products.delete');

    //store
    Route::get('/store', [StoreController::class, 'index'])->name('store.index');
    Route::get('/store/data', [StoreController::class, 'getDataStore'])->name('store.get');
    Route::post('/store', [StoreController::class, 'store'])->name('store.store');
    Route::put('/store/{id}', [StoreController::class, 'update'])->name('store.update');
    Route::delete('/store/{id}', [StoreController::class, 'destroy'])->name('store.delete');

        // stock
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/data', [StockController::class, 'getDataStock'])->name('stock.get');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
    Route::delete('/stock/{id}', [StockController::class, 'destroy'])->name('stock.delete');


    Route::get('/temp', function(){
        echo 'Coming Soon';
    })->name('temp');
});

require __DIR__.'/auth.php';
