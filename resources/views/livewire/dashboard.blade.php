<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\Staff;
use App\Models\Attendance;
use Carbon\Carbon;
use function Livewire\Volt\{state, with};

with(
    fn() => [
        'totalSalesToday' => Order::whereDate('created_at', now('Asia/Jakarta'))->sum('total_price'),
        'totalOrdersToday' => Order::whereDate('created_at', now('Asia/Jakarta'))->count(),
        'totalOrdersFinishedToday' => Order::whereDate('created_at', now('Asia/Jakarta'))->where('status', 'completed')->count(),
        'outOfStockCount' => Product::where('is_available', 0)->count(),
        'recentOrders' => Order::latest()->take(5)->get(),
        'lowStockProducts' => Product::where('is_available', 0)->take(5)->get(),
        'StaffOnDuty' => Attendance::whereDate('clock_in', now('Asia/Jakarta'))->whereNull('clock_out')->count(),
    ],
);
?>

<div wire:poll.30s class="h-screen overflow-y-auto bg-gray-50 min-h-screen">
    {{-- Header --}}
    <x-header title="Dashboard" subtitle="Dashboard POS Titik Koma" />

    <div class="max-w-7xl mx-auto">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-4">
            <x-stat-card title="Earnings" value="Rp {{ number_format($totalSalesToday, 0, ',', '.') }}"
                iconColor="secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-stat-card>

            <x-stat-card title="Completed Orders" value="{{ $totalOrdersFinishedToday }}/{{ $totalOrdersToday }}"
                iconColor="secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </x-stat-card>

            <x-stat-card title="Out of Stock" value="{{ $outOfStockCount }} Item" iconColor="secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </x-stat-card>

            <x-stat-card title="Staff On Duty" value="{{ $StaffOnDuty }} Staff" iconColor="primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </x-stat-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-8 gap-6">
            {{-- Chart Section --}}
            <livewire:sales-chart class="col-span-3" />

            {{-- Recent Activity Table --}}
            <livewire:recent-orders class="col-span-3" />

            {{-- Out of Stock --}}
            <livewire:out-of-stock class="col-span-2" />
        </div>
    </div>
</div>
