<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductOption;

new class extends Component {
    public $search = '';
    public $selectedCategory = null;
    public $cart = [];

    public $showCustomizer = false;
    public $selectedProduct = null;
    public $customForm = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function setCategory($id = null)
    {
        $this->selectedCategory = $id;
    }

    public function openCustomizer($productId)
    {
        $product = Product::with('optionGroups.options')->find($productId);

        if (!$product || !$product->is_available) {
            return;
        }

        $this->selectedProduct = $product;
        $this->customForm = [];

        foreach ($product->optionGroups as $group) {
            $this->customForm[$group->id] = $group->options->first()->id ?? null;
        }

        $this->showCustomizer = true;
    }

    public function confirmAddToCart()
    {
        if (!$this->selectedProduct) {
            return;
        }

        $selectedOptionsDetails = ProductOption::whereIn('id', array_values($this->customForm))->get();
        $totalExtraPrice = $selectedOptionsDetails->sum('extra_price');
        $finalPrice = $this->selectedProduct->price + $totalExtraPrice;
        $optionLabels = $selectedOptionsDetails->pluck('option_name')->implode(', ');
        $uniqueId = $this->selectedProduct->id . '-' . md5(json_encode($this->customForm));

        if (isset($this->cart[$uniqueId])) {
            $this->cart[$uniqueId]['qty']++;
        } else {
            $this->cart[$uniqueId] = [
                'id' => $this->selectedProduct->id,
                'unique_key' => $uniqueId,
                'name' => $this->selectedProduct->name,
                'price' => $finalPrice,
                'qty' => 1,
                'image' => $this->selectedProduct->image,
                'option_text' => $optionLabels,
                'options_ids' => $this->customForm,
            ];
        }

        $this->syncSession();
        $this->showCustomizer = false;
    }

    public function incrementQty($uniqueId)
    {
        if (isset($this->cart[$uniqueId])) {
            $this->cart[$uniqueId]['qty']++;
            $this->syncSession();
        }
    }

    public function decrementQty($uniqueId)
    {
        if (isset($this->cart[$uniqueId])) {
            if ($this->cart[$uniqueId]['qty'] > 1) {
                $this->cart[$uniqueId]['qty']--;
            } else {
                unset($this->cart[$uniqueId]);
            }
            $this->syncSession();
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->syncSession();
    }

    private function syncSession()
    {
        session()->put('cart', $this->cart);
    }

    public function with()
    {
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $tax = $subtotal * 0.1;

        return [
            'products' => Product::query()->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))->get(),
            'categories' => Category::all(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ];
    }
}; ?>

<div class="flex h-screen w-full overflow-hidden bg-gray-50/50">
    <div class="flex-1 overflow-y-auto min-w-0">
        {{-- Header --}}
        <x-header title="Cashier">
            <x-search-bar placeholder="Cari menu item" model="search" class="w-full md:w-96" />
        </x-header>
        {{-- Konten Utama --}}
        <div class="max-w-7xl mx-auto px-6">
            {{-- Kategori Tabs --}}
            <div class="flex gap-2 mb-6 overflow-x-auto scrollbar-hide">
                <button wire:click="setCategory(null)"
                    class="px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ !$selectedCategory ? 'bg-primary text-white shadow-md shadow-primary/20' : 'bg-white text-gray-500 hover:bg-gray-100 border border-gray-100' }}">
                    Semua
                </button>
                @foreach ($categories as $category)
                    <button wire:click="setCategory({{ $category->id }})"
                        class="px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedCategory == $category->id ? 'bg-primary text-white shadow-md' : 'bg-white text-gray-500 border border-gray-100' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>

            {{-- Grid Produk --}}
            <div class="grid grid-cols-1 sm:grid-cols- md:grid-cols-6 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @forelse($products as $product)
                    <div wire:click="openCustomizer({{ $product->id }})" wire:key="product-{{ $product->id }}">
                        <x-product-item-card :product="$product" />
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-gray-400 text-sm italic">Menu tidak ditemukan...
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <aside class="flex flex-col h-screen bg-white shadow-xl border-l border-gray-200 w-full max-w-85 ml-auto">
        {{-- Header Keranjang --}}
        <div class="p-3 flex items-center justify-between border-b border-gray-100 gap-2">
            <button
                class="flex items-center gap-1.5 bg-secondary px-2.5 py-2 rounded-lg text-xs font-semibold text-gray-200 hover:bg-primary whitespace-nowrap transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Customer
            </button>

            <div class="flex gap-1.5">
                <button wire:click="clearCart"
                    class="p-2 bg-gray-100 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition"
                    title="Clear Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button class="p-2 bg-secondary rounded-lg text-gray-100 hover:bg-primary transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Daftar Item Keranjang --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            @forelse($cart as $uniqueId => $item)
                <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-2.5">
                    <div class="flex justify-between items-start">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[11px] font-black text-secondary truncate uppercase">
                                {{ $item['name'] }}
                            </h4>
                            @if (!empty($item['option_text']))
                                <p class="text-[9px] text-primary font-bold uppercase tracking-tight">
                                    {{ $item['option_text'] }}
                                </p>
                            @endif
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-2xs font-bold text-gray-500">
                                    {{ $item['qty'] }}x Rp{{ number_format($item['price'], 0, ',', '.') }}
                                </span>

                                <div class="flex items-center gap-2">
                                    <button wire:click="decrementQty('{{ $uniqueId }}')"
                                        class="p-1 bg-white border rounded shadow-sm text-gray-400 hover:text-red-500">-</button>
                                    <span class="text-xs font-bold">{{ $item['qty'] }}</span>
                                    <button wire:click="incrementQty('{{ $uniqueId }}')"
                                        class="p-1 bg-white border rounded shadow-sm text-gray-400 hover:text-primary">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-gray-300 opacity-50">
                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="text-xs font-bold uppercase">Cart is empty</p>
                </div>
            @endforelse
        </div>

        {{-- Rincian Pembayaran --}}
        <div class="bg-gray-50 p-4 border-t border-gray-200">
            <div
                class="bg-white border border-gray-200 rounded-xl p-2.5 flex justify-between items-center mb-4 shadow-sm">
                <span class="text-2xs uppercase font-black text-gray-400">Add Extras</span>
                <div class="flex gap-3 text-2xs font-bold text-secondary uppercase">
                    <button class="hover:text-primary transition">Disc</button>
                    <button class="hover:text-primary transition">Coupon</button>
                    <button class="hover:text-primary transition">Note</button>
                </div>
            </div>

            <div class="space-y-2 mb-4 px-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="text-secondary">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tax (10%)</span>
                    <span class="text-secondary">Rp{{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-200 mt-2">
                    <span class="text-xs font-black text-secondary uppercase italic">Payable Amount</span>
                    <span class="text-lg font-black text-primary">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button
                    class="flex flex-col items-center justify-center bg-secondary text-white py-3 rounded-xl font-bold hover:bg-secondary/90 active:scale-95 transition-all text-2xs uppercase shadow-md">
                    <svg class="w-5 h-5 mb-1 opacity-80" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Hold Order
                </button>

                <button
                    class="flex flex-col items-center justify-center bg-primary text-white py-3 rounded-xl font-bold hover:brightness-110 active:scale-95 transition-all text-2xs uppercase shadow-md shadow-primary/20">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pay Now
                </button>
            </div>
        </div>
    </aside>
    @if ($showCustomizer && $selectedProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div
                class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">

                {{-- Header Modal --}}
                <div class="p-3 border-b border-gray-100 bg-secondary flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900 uppercase tracking-tight">
                        {{ $selectedProduct->name }}
                    </h3>
                    <button wire:click="$set('showCustomizer', false)"
                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="p-6 space-y-8 max-h-[60vh] overflow-y-auto bg-white">
                    @foreach ($selectedProduct->optionGroups as $group)
                        <div class="space-y-3">
                            <label class="block text-xs font-black text-primary uppercase tracking-widest">
                                {{ $group->name }}
                            </label>

                            <div class="grid grid-cols-4 gap-3 mb-4">
                                @foreach ($group->options as $option)
                                    <button wire:click="$set('customForm.{{ $group->id }}', {{ $option->id }})"
                                        class="relative p-4 rounded-2xl border-2 text-left transition-all duration-200
                                    {{ ($customForm[$group->id] ?? '') == $option->id
                                        ? 'border-secondary bg-secondary/5 text-secondary shadow-md'
                                        : 'border-gray-100 bg-gray-50 text-gray-700 hover:border-gray-200 hover:bg-gray-100' }}">

                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold text-sm leading-tight">{{ $option->option_name }}</span>
                                            @if ($option->extra_price > 0)
                                                <span class="text-2xs mt-1 font-medium opacity-70">
                                                    +Rp{{ number_format($option->extra_price, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-2xs mt-1 font-medium opacity-70">
                                                    No Extra Charge
                                                </span>
                                            @endif
                                        </div>
                                        @if (($customForm[$group->id] ?? '') == $option->id)
                                            <div class="absolute top-2 right-2">
                                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if ($selectedProduct->optionGroups->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-400 text-sm italic">Produk ini tidak memiliki opsi tambahan.</p>
                        </div>
                    @endif
                </div>

                {{-- Footer Modal --}}
                <div class="p-3 bg-gray-50 border-t border-gray-100">
                    <button wire:click="confirmAddToCart"
                        class="w-full bg-primary text-white py-2 rounded-full font-black uppercase tracking-widest shadow-lg shadow-primary/30 hover:brightness-110 active:scale-95 transition-all">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
