<?php

use App\Models\Product;
use function Livewire\Volt\{with};

with(
    fn() => [
        'lowStockProducts' => Product::where('is_available', 0)->take(5)->get(),
    ],
);
?>

<div {{ $attributes->merge(['class' => 'bg-white p-6 rounded-2xl shadow-sm border border-gray-100']) }}>
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-gray-600 uppercase text-xs tracking-wider flex items-center text-red-600">
            <svg xmlns="http://www.w3.org/2000/01/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Out of Stocks
        </h3>
        <span class="text-2xs bg-red-50 text-red-500 px-2 py-0.5 rounded-full font-bold">
            {{ $lowStockProducts->count() }} Urgent
        </span>
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
                        <p class="text-2xs text-gray-400 uppercase tracking-tighter">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </p>
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

    @if ($lowStockProducts->count() > 0)
        <div class="mt-5">
            <a href="#"
                class="block w-full text-center py-2 bg-gray-50 hover:bg-gray-100 text-gray-500 text-2xs font-bold uppercase rounded-xl transition-colors">
                Kelola Inventaris
            </a>
        </div>
    @endif
</div>
