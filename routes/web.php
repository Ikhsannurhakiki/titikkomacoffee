<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

// 1. ROOT
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    if (session()->has('current_staff')) {
        $role = data_get(session('current_staff'), 'position');
        return ($role === 'kitchen') ? redirect()->route('order-list') : redirect()->route('pos');
    }

    return redirect()->route('role-login');
});

Volt::route('/role-login', 'role-login')->name('role-login');

// 2. SCENARIO: STAFF CASHIER
Route::middleware(['staff.access:cashier'])->group(function () {
    Volt::route('/pos-screen', 'pos-screen')->name('pos');
    Volt::route('/product-cutomizer', 'product-cutomizer')->name('product-cutomizer');
});

// 3. STAFF KITCHEN
Route::middleware(['staff.access:kitchen'])->group(function () {
    Volt::route('/product-manager', 'product-manager')->name('product-manager');
});

// 4. ADMIN ONLY 
Route::middleware(['auth', 'verified'])->group(function () {

    Volt::route('/products', 'product-screen')->name('products');
    Volt::route('/staff-manager', 'staff-manager')->name('staff-manager');
    Volt::route('/staff-active', 'staff-active')->name('staff-active');
    Volt::route('/product-form', 'product-form')->name('product-form');    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 5. ALL
Route::middleware(['staff.access:cashier,kitchen'])->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
    Volt::route('/order-list', 'order-list')->name('order-list');
    Volt::route('/attendance', 'attendance')->name('attendance');
});

require __DIR__ . '/auth.php';
