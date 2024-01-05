<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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

Route::resource('users', UserController::class)->middleware('auth', 'verified');

// TODO: 検証用で設定したルートなのであとで削除する
Route::resource('upload', UploadController::class);

Route::resource('restaurants', RestaurantController::class)->middleware('auth', 'verified');
Route::post('restaurants/{restaurant}', [RestaurantController::class, 'update'])->name('restaurants.formUpdate');
Route::post('restaurants/{restaurant}/attach', [RestaurantController::class, 'attachFavorite'])->name('restaurants.attach');
Route::delete('restaurants/{restaurant}/detach', [RestaurantController::class, 'detachFavorite'])->name('restaurants.detach');

Route::resource('schedules', ScheduleController::class)->middleware('auth', 'verified');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/home', [RestaurantController::class, 'home'])
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
