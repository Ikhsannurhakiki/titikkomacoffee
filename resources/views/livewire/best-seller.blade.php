<?php

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use function Livewire\Volt\{with};

with(
    fn() => [
        'bestSellers' => DB::table('order_items')->join('products', 'order_items.product_id', '=', 'products.id')->select('products.name', 'products.id', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_revenue'))->groupBy('products.id', 'products.name')->orderByDesc('total_qty')->take(5)->get(),
    ],
);
?>

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden']) }}
    wire:ignore>
    <div class="p-4 border-b bg-primary/15 border-primary/25 flex justify-between items-center group">
        <h3 class="font-bold text-primary uppercase text-xs tracking-wider">
            <span class="mr-2">🔥</span> Produk Terlaris
        </h3>
        <a href="{{ route('dashboard') }}" wire:navigate
            class="p-1 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all duration-300"
            title="Lihat Detail">
            <svg xmlns="http://www.w3.org/2000/01/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
            </svg>
        </a>
    </div>

    <div class="space-y-5 p-4">
        @forelse($bestSellers as $index => $item)
            @php
                $product = \App\Models\Product::find($item->id);
                // Ranking colors
                $rankClasses = [
                    0 => 'bg-amber-100 text-amber-600', // Gold
                    1 => 'bg-slate-100 text-slate-500', // Silver
                    2 => 'bg-orange-100 text-orange-600', // Bronze
                ];
                $badgeClass = $rankClasses[$index] ?? 'bg-gray-50 text-gray-400';
            @endphp

            <div class="flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $product?->getFirstMediaUrl('thumbnail') ?: asset('images/logo.png') }}"
                                class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                        </div>
                        <div
                            class="absolute -top-2 -left-2 w-5 h-5 {{ $badgeClass }} rounded-full flex items-center justify-center text-[10px] font-black shadow-sm border border-white">
                            {{ $index + 1 }}
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-700 truncate w-32 leading-none mb-1">
                            {{ $item->name }}
                        </span>
                        <p class="text-[10px] text-gray-400 font-medium">
                            {{ number_format($item->total_qty) }} Terjual
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs font-black text-gray-800">
                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                    </p>
                    <div class="w-16 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                        <div class="h-full bg-primary"
                            style="width: {{ ($item->total_qty / $bestSellers->first()->total_qty) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 text-xs italic py-4">Belum ada data penjualan</p>
        @endforelse
    </div>
</div>
