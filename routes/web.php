<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

/**
 * Web Routes
 *
 * Here is where you can register web routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * contains the "web" middleware group. Now create something great!
 */
Route::get('/', function () {
    return view('welcome');
});

/**
 * Dashboard route, only accessible by authenticated and verified users.
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Authenticated routes group.
 */
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User management routes
    Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);

    // Category management routes
    Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Item management routes
    Route::resource('items', ItemController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Sale management routes
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'edit', 'destroy']);
});

require __DIR__ . '/auth.php';
