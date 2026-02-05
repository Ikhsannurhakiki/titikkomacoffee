<?php

use App\Models\Product;
use function Livewire\Volt\{with};

with(
    fn() => [
        'lowStockProducts' => Product::where('is_available', 0)->take(5)->get(),
    ],
);
?>

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden']) }}
    wire:poll.10s.visible.keep-alive>
    <div class="p-4 border-b bg-primary/15 border-primary/25 flex justify-between items-center group">
        <h3 class="font-bold uppercase text-xs tracking-wider flex items-center text-primary">
            <svg xmlns="http://www.w3.org/2000/01/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Out of Stocks
        </h3>

        <div class="flex items-center gap-2">
            <span class="text-[10px] bg-red-500 text-white px-2 py-0.5 rounded-full font-black">
                {{ $lowStockProducts->count() }}
            </span>

            {{-- Ikon Navigasi Detail --}}
            <a href="{{ route('dashboard') }}" wire:navigate
                class="p-1 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all duration-300"
                title="Lihat Detail">
                <svg xmlns="http://www.w3.org/2000/01/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                </svg>
            </a>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($lowStockProducts as $product)
            <div
                class="group flex items-center justify-between p-3 bg-gray-50/50 rounded-xl border border-transparent hover:border-red-100 hover:bg-red-50/30 transition-all">
                <div class="flex items-center">
                    <div class="relative">
                        <img src="{{ $product->getFirstMediaUrl('thumbnail') ?: asset('images/logo.png') }}"
                            class="w-12 h-12 rounded-lg object-cover grayscale opacity-70 group-hover:opacity-100 transition shadow-sm">
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white rounded-full">
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-bold text-gray-700 group-hover:text-red-700 transition-colors">
                            {{ $product->name }}</p>

                    </div>
                </div>
            </div>
        @empty
            <div
                class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-gray-100 rounded-2xl">
                <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                    <span class="text-xl">✅</span>
                </div>
                <p class="text-gray-400 text-sm font-medium italic">Semua persediaan masih aman!</p>
            </div>
        @endforelse
    </div>
</div>
