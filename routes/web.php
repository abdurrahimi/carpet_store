<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PenjualanController;
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

Route::post('/deploy', function (Request $request) {
    return response('Deploy bro', 200);
});

Route::get('/invoice/download/{id}', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');

Route::get('/', function () {
    return redirect('/dashboard');
    /* return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]); */
});

Route::get('test', function(){
    return view('surat-jalan');
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
    Route::post('/users/mass', [UsersController::class, 'massDestroy'])->name('users.massDestroy');

    //Customer
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/data', [CustomerController::class, 'getDataCustomer'])->name('customer.data');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::post('/customer/delete', [CustomerController::class, 'delete'])->name('customer.delete');
    Route::post('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');

    //Product Category
    Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/data', [CategoryController::class, 'getData'])->name('category.data');
    Route::get('/category/color/{category_id}', [CategoryController::class, 'getDataColor'])->name('category.color');
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::post('/category/delete', [CategoryController::class, 'delete'])->name('category.delete');
    Route::post('/category/{id}', [CategoryController::class, 'update'])->name('category.update');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/data', [ProductController::class, 'getDataProduct'])->name('products.get');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'delete'])->name('products.delete');
    Route::post('/product/import', [ProductController::class, 'importExcel'])->name('product.import');
    Route::post('/products/delete-bulk', [ProductController::class, 'deleteBulk'])->name('products.deleteBulk');


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
    Route::get('/stock/check', [StockController::class, 'checkStock'])->name('stock.check');
    Route::get('/stock/check/detail', [StockController::class, 'getDataStockDetail'])->name('stock.check.detail');
    Route::get('/stock/check/data', [StockController::class, 'getStockLists'])->name('stock.check.data');

    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/kasir', [PenjualanController::class, 'kasir'])->name('penjualan.kasir');

    //order
    Route::post('/order', [OrderController::class, 'order'])->name('order.create');
    Route::get('/order-status-history', [OrderController::class, 'getStatusHistory'])->name('order.getStatusHistory');
    Route::post('/order-approval/{id}', [OrderController::class, 'approve'])->name('order.approve');
    Route::post('/order-reject/{id}', [OrderController::class, 'reject'])->name('order.reject');
    Route::post('/order-attachment/{id}', [OrderController::class, 'addComments'])->name('order.addAttachment');
    Route::get('/order/detail/{id}', [PenjualanController::class, 'getDetailPenjualan'])->name('order.detail');
    Route::get('/order/surat-jalan', [PenjualanController::class, 'suratJalan'])->name('order.printSuratJalan');


    //Approval
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval', [ApprovalController::class, 'approvalAction'])->name('approval.action');

    //Company
    Route::get('/companies', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/companies/data', [CompanyController::class, 'getData'])->name('company.data');
    Route::post('/companies', [CompanyController::class, 'store'])->name('company.store');
    Route::post('/companies/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/companies', [CompanyController::class, 'delete'])->name('company.delete');

    //LPBB
    Route::get('/lpbb', [\App\Http\Controllers\LpbbController::class, 'index'])->name('lpbb.index');
    Route::get('/lpbb/data', [\App\Http\Controllers\LpbbController::class, 'getData'])->name('lpbb.data');
    Route::post('/lpbb', [\App\Http\Controllers\LpbbController::class, 'store'])->name('lpbb.store');


    Route::get('/temp', function () {
        echo 'Coming Soon';
    })->name('temp');
});

require __DIR__ . '/auth.php';
