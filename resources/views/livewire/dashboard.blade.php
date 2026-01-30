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
        'activeStaffCount' => Staff::where('is_active', 1)->count(),
        'StaffOnDuty' => Attendance::where('clock_in', '>=', now('Asia/Jakarta')->copy()->startOfDay())
            ->where('clock_in', '!=', null)
            ->where('clock_out', null)
            ->count(),
    ],
);
?>

<div wire:poll.30s class="h-screen overflow-y-auto bg-gray-50 min-h-screen">
    {{-- Header --}}
    <x-header title="Dashboard" subtitle="Dashboard POS" />

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-stat-card title="Penjualan Hari Ini" value="Rp {{ number_format($totalSalesToday, 0, ',', '.') }}"
                iconColor="secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-stat-card>

            <x-stat-card title="Total Pesanan Hari Ini"
                value="{{ $totalOrdersToday }}/{{ $totalOrdersFinishedToday }} Order Completed" iconColor="secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </x-stat-card>

            <x-stat-card title="Produk Habis" value="{{ $outOfStockCount }} Item" iconColor="secondary">
                <svg class="w-6 h-6{{ $outOfStockCount > 0 ? 'text-red-600' : 'text-gray-400' }}" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </x-stat-card>

            <x-stat-card title="Staff Hadir Hari Ini" value="{{ $StaffOnDuty }} Orang" iconColor="primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </x-stat-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 uppercase text-xs tracking-wider">Aktivitas Terakhir</h3>
                    <a href="#" class="text-primary text-2xs font-bold hover:underline">LIHAT SEMUA</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50/50">
                            <tr class="text-gray-400 uppercase text-2xs tracking-widest">
                                <th class="px-6 py-3 font-semibold text-center">Inv</th>
                                <th class="px-6 py-3 font-semibold">Total</th>
                                <th class="px-6 py-3 font-semibold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-center text-gray-400">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">Rp
                                        {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="px-2 py-1 bg-green-50 text-green-600 rounded-lg text-2xs font-black shadow-sm">PAID</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada
                                        transaksi hari ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-600 uppercase text-xs tracking-wider flex items-center text-red-600">
                        Stok Perlu Diisi
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($lowStockProducts as $product)
                        <div
                            class="group flex items-center justify-between p-3 bg-gray-50/50 rounded-xl border border-transparent hover:border-red-100 hover:bg-red-50/30 transition-all">
                            <div class="flex items-center">
                                <div class="relative">
                                    <img src="{{ $product->getFirstMediaUrl('thumbnail') ?: asset('images/logo.png') }}"
                                        class="w-12 h-12 rounded-lg object-cover grayscale opacity-70 group-hover:opacity-100 transition shadow-sm">
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-bold text-gray-700">{{ $product->name }}</p>
                                    <p class="text-2xs text-gray-400 uppercase tracking-tighter">
                                        {{ $product->category->name ?? 'Uncategorized' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-[9px] font-black uppercase">Kosong</span>
                            </div>
                        </div>
                    @empty
                        <div
                            class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-2xl">
                            <span class="text-2xl mb-2">✅</span>
                            <p class="text-gray-400 text-sm font-medium italic">Semua persediaan masih aman!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
