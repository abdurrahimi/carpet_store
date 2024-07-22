<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
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
    $secret = 'uQF10bbaNHPljw8'; // Ganti dengan secret webhook GitHub Anda
    $payload = $request->getContent();
    $signature = $request->header('X-Hub-Signature');

    // Verifikasi signature
    $hash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
    if (hash_equals($hash, $signature)) {
        // Parse payload
        $data = json_decode($payload, true);
        if ($data['ref'] === 'refs/heads/feat/revamp') { // Ganti dengan branch yang diinginkan
            // Menjalankan deploy
            $output = [];
            $returnVar = 0;
            exec('cd /home/tops1919/public_html/admin-panel && git pull origin feat/revamp 2>&1', $output, $returnVar);

            // Log output untuk debugging
            Log::info('Deploy output: ' . implode("\n", $output));

            if ($returnVar === 0) {
                return response('Deploy successful', 200);
            } else {
                Log::error('Deploy failed with return code: ' . $returnVar);
                return response('Deploy failed', 500);
            }
        }
    }

    Log::error('Deployment failed. Invalid signature or branch.');
    return response('Deploy failed', 400);
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
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');

    //store
    Route::get('/store/data', [StoreController::class, 'getDataStore'])->name('store.get');

    Route::get('/temp', function(){
        echo 'Coming Soon';
    })->name('temp');
});

require __DIR__.'/auth.php';
