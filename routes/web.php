<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/products', 'product-screen')->name('products');
Volt::route('/pos', 'pos-screen')->name('pos');
Volt::route('/staff-active', 'staff-active')->name('staff-active');
Volt::route('/product-form', 'product-form')->name('product-form');
Volt::route('/product-manager', 'product-manager')->name('product-manager');
Volt::route('/staff-manager', 'staff-manager')->name('staff-manager');
Volt::route('/dashboard', 'dashboard')->name('dashboard');
Volt::route('/attendance', 'attendance')->name('attendance');
Volt::route('/product-cutomizer', 'product-cutomizer')->name('product-cutomizer');
Volt::route('/order-list', 'order-list')->name('order-list');
Volt::route('/role-login', 'role-login')->name('role-login');

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/home', function () {
//     return view('home');
// });
// ->middleware(['auth', 'verified'])->name('home');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__ . '/auth.php';
