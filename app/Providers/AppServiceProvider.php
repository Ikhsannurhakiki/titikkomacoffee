<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');
        Volt::mount([
            resource_path('views/livewire'),
            resource_path('views/pages'),
        ]);
        view()->composer('*', function ($view) {
            $staff = session('current_staff');

            $view->with([
                'isKitchen' => data_get($staff, 'position') === 'kitchen',
                'isCashier' => data_get($staff, 'position') === 'cashier',
                'isAdmin'   => Auth::check() && Auth::user()->role === 'admin',
                'canManageOrder' => (data_get($staff, 'position') === 'kitchen') || (Auth::check() && Auth::user()->role === 'admin'),
            ]);
        });
    }
}
