<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    public $search = '';
    public $selectedCategory = null;

    // Fungsi untuk memfilter kategori
    public function setCategory($id = null)
    {
        $this->selectedCategory = $id;
    }

    // Logic pengambilan data (Computed)
    public function with()
    {
        return [
            'products' => Product::query()->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))->get(),
            'categories' => Category::all(),
        ];
    }

    public function rendering($view)
    {
        // Jika kamu menggunakan @yield('content') di layouts/app.blade.php
        $view->extends('layouts.app')->section('content');
    }
}; ?>

<div class="flex flex-col h-full bg-gray-50/50">
    <header class="p-6 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-4 items-center justify-between mb-6">
            <h1 class="text-2xl font-black text-secondary tracking-tight">Titik Koma <span
                    class="text-primary text-3xl">.</span></h1>

            {{-- Search Bar --}}
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live="search" type="text"
                    class="block w-full pl-10 pr-3 py-3 border-none bg-gray-100 rounded-2xl focus:ring-2 focus:ring-primary/20 transition text-sm font-bold placeholder:text-gray-400"
                    placeholder="Cari kopi favoritmu...">
            </div>
        </div>

        {{-- Category Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <button wire:click="setCategory(null)"
                class="px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all {{ !$selectedCategory ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                All Menu
            </button>
            @foreach ($categories as $category)
                <button wire:click="setCategory({{ $category->id }})"
                    class="px-6 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all {{ $selectedCategory == $category->id ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @forelse($products as $product)
                <button wire:click="$dispatch('addToCart', { productId: {{ $product->id }} })"
                    class="group bg-white p-3 rounded-[2rem] border border-transparent hover:border-primary/20 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 text-left relative">

                    {{-- Badge Discount (Opsional) --}}
                    @if ($product->discount)
                        <div
                            class="absolute top-5 left-5 z-10 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-lg">
                            -{{ $product->discount }}%
                        </div>
                    @endif

                    {{-- Image Container --}}
                    <div class="relative mb-3 aspect-square overflow-hidden rounded-[1.5rem] bg-gray-100">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/dummy-item.png') }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>

                    {{-- Product Info --}}
                    <div class="px-1">
                        <h3
                            class="font-black text-gray-800 text-sm leading-tight mb-1 truncate group-hover:text-primary transition-colors">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-center justify-between mt-2">
                            <span
                                class="text-xs font-black text-primary">${{ number_format($product->price, 2) }}</span>
                            <div
                                class="w-8 h-8 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </button>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">Menu tidak ditemukan</p>
                </div>
            @endforelse
        </div>
    </main>
</div>
