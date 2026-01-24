<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    public $search = '';
    public $selectedCategory = null;

    public function setCategory($id = null)
    {
        $this->selectedCategory = $id;
    }

    public function with()
    {
        return [
            'products' => Product::query()->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))->get(),
            'categories' => Category::all(),
        ];
    }
}; ?>

<div class="flex h-full w-full overflow-hidden bg-gray-50/50">
    <div class="flex-1 p-6 overflow-y-auto min-w-0">
        {{-- Header & Search --}}
        <div class="flex justify-between items-center mb-6">
            <div class="relative w-64">
                <input type="text" wire:model.live="search" placeholder="Cari produk..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-primary transition-all shadow-sm">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- Kategori Tabs --}}
        <div class="flex gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
            <button wire:click="setCategory(null)"
                class="px-5 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ !$selectedCategory ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white text-gray-500 hover:bg-gray-100' }}">
                Semua
            </button>
            @foreach ($categories as $category)
                <button wire:click="setCategory({{ $category->id }})"
                    class="px-5 py-2 rounded-xl text-sm font-bold transition-all whitespace-nowrap {{ $selectedCategory == $category->id ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'bg-white text-gray-500 hover:bg-gray-100' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Grid Produk --}}
        <div class="grid grid-cols-2 md:grid-cols-6 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            @forelse($products as $product)
                <x-product-item-card :product="$product" wire:click="addToCart({{ $product->id }})" />
            @empty
                <div class="col-span-full py-16 text-center">
                    <p class="text-gray-400 font-medium">Produk tidak ditemukan...</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Keranjang Kanan --}}
    <aside class="w-80 bg-white border-l shrink-0 overflow-y-auto">
        @include('orders')
    </aside>
</div>
