<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ItemController,
    ProfileController,
    UserController,
    SaleController,
    PaymentController
};

// Welcome page
Route::get('/', fn() => view('welcome'));

// Dashboard
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Routes with authentication
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::resource('users', UserController::class);

    // Items
    Route::resource('items', ItemController::class);

    // Sales
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Payments
    Route::get('/payments/create/{sale}', [PaymentController::class, 'create'])->name('payments.create');
    Route::resource('payments', PaymentController::class)->only(['index', 'store']);
});

// Auth scaffolding routes (Jetstream/Breeze/etc)
require __DIR__.'/auth.php';
