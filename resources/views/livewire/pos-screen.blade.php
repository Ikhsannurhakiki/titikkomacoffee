<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    public $search = '';
    public $selectedCategory = null;
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function setCategory($id = null)
    {
        $this->selectedCategory = $id;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return;
        }

        if (!$product->is_available) {
            session()->flash('error', 'Produk ini sedang Out of Stock!');
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
                'image' => $product->image,
            ];
        }

        $this->syncSession();
    }

    public function incrementQty($productId)
    {
        $this->cart[$productId]['qty']++;
        $this->syncSession();
    }

    public function decrementQty($productId)
    {
        if ($this->cart[$productId]['qty'] > 1) {
            $this->cart[$productId]['qty']--;
        } else {
            unset($this->cart[$productId]);
        }
        $this->syncSession();
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

    <div class="flex-1 p-4 overflow-y-auto min-w-0">
        {{-- Kategori Tabs & Search Bar --}}
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2 scrollbar-hide">
            <div class="relative w-72">
                <input type="text" wire:model.live="search" placeholder="Cari menu..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-primary transition-all shadow-sm text-sm font-medium">
                <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
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
                <div wire:click="addToCart({{ $product->id }})" wire:key="product-{{ $product->id }}"
                    @disabled(!$product->is_available)>
                    <x-product-item-card :product="$product" />
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-gray-400 text-sm italic">Menu tidak ditemukan...</div>
            @endforelse
        </div>
    </div>

    <aside class="flex flex-col h-screen bg-white shadow-xl border-l border-gray-200 w-full max-w-[340px] ml-auto">
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
            @forelse($cart as $id => $item)
                <div class="bg-gray-50/50 border border-gray-100 rounded-xl p-2.5">
                    <div class="flex justify-between items-start mb-2">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-[11px] font-black text-secondary truncate uppercase">{{ $item['name'] }}
                            </h4>
                            <span
                                class="text-[10px] font-bold text-primary">Rp{{ number_format($item['price'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center bg-white border border-gray-200 rounded-lg ml-2">
                            <button wire:click="decrementQty({{ $id }})"
                                class="p-1 px-2 hover:bg-gray-50 text-secondary">-</button>
                            <span
                                class="px-2 text-[10px] font-black text-secondary border-x border-gray-100">{{ $item['qty'] }}</span>
                            <button wire:click="incrementQty({{ $id }})"
                                class="p-1 px-2 hover:bg-gray-50 text-secondary">+</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center opacity-20 italic text-sm py-20">
                    <p>No items added</p>
                </div>
            @endforelse
        </div>

        {{-- Rincian Pembayaran --}}
        <div class="bg-gray-50 p-4 border-t border-gray-200">
            <div
                class="bg-white border border-gray-200 rounded-xl p-2.5 flex justify-between items-center mb-4 shadow-sm">
                <span class="text-[10px] uppercase font-black text-gray-400">Add Extras</span>
                <div class="flex gap-3 text-[10px] font-bold text-secondary uppercase">
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
                    class="flex flex-col items-center justify-center bg-secondary text-white py-3 rounded-xl font-bold hover:bg-secondary/90 active:scale-95 transition-all text-[10px] uppercase shadow-md">
                    <svg class="w-5 h-5 mb-1 opacity-80" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Hold Order
                </button>

                <button
                    class="flex flex-col items-center justify-center bg-primary text-white py-3 rounded-xl font-bold hover:brightness-110 active:scale-95 transition-all text-[10px] uppercase shadow-md shadow-primary/20">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Pay Now
                </button>
            </div>
        </div>
    </aside>
</div>
