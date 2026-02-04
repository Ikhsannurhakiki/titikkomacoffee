<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Staff;
use App\Models\Order;
use App\Models\ProductOption;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $search = '';
    public $selectedCategory = null;
    public $cart = [];
    public $customer_name = null;
    public $table_number = null;

    public $showCustomizer = false;
    public $selectedProduct = null;
    public $showReceipt = false;
    public $customForm = [];
    public $latestOrder = null;

    public $currentStaff;
    public $showPaymentModal = false;
    public $paidAmountInput = 0;
    public $changeAmount = 0;

    public function mount()
    {
        $this->validateCart();
        $this->currentStaff = Staff::find(session('current_staff.id'));
        $this->cart = session()->get('cart', []);
    }

    public function setCategory($id = null)
    {
        $this->selectedCategory = $id;
    }

    public function validateCart()
    {
        foreach ($this->cart as $key => $item) {
            $productStatus = \App\Models\Product::where('id', $item['id'])->value('status');
            if ($productStatus === 'out_of_stock') {
                unset($this->cart[$key]);
                $this->dispatch('notify', ['message' => $item['name'] . ' removed from cart as it is now out of stock.']);
            }
        }
        $this->syncSession();
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
        if ($product->optionGroups->isEmpty()) {
            $this->confirmAddToCart();
            return;
        }

        $this->showCustomizer = true;
    }

    public function confirmAddToCart()
    {
        if (!$this->selectedProduct) {
            return;
        }

        if ($this->selectedProduct->status === 'out_of_stock') {
            $this->showCustomizer = false;
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
        $this->table_number = null;
        $this->customer_name = null;
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

    public function openPayment()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong!');
            return;
        }

        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $total = $subtotal + $subtotal * 0.1;

        $this->paidAmountInput = $total;
        $this->changeAmount = 0;
        $this->showPaymentModal = true;
    }

    public function updatedPaidAmountInput($value)
    {
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $total = $subtotal + $subtotal * 0.1;
        $this->changeAmount = max(0, (float) $value - $total);
    }

    public function payNow()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang masih kosong!');
            return;
        }
        $subtotal = collect($this->cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $tax = $subtotal * 0.1;
        $total = $subtotal + $tax;
        if ((float) $this->paidAmountInput < $total) {
            session()->flash('error', 'Uang yang dimasukkan kurang dari total tagihan!');
            return;
        }
        DB::beginTransaction();
        try {
            $order = Order::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'staff_id' => $this->currentStaff->id,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_price' => $total,
                'paid_amount' => (float) $this->paidAmountInput,
                'change_amount' => (float) $this->changeAmount,
                'payment_method' => 'cash',
                'status' => 'processing',
                'customer_name' => $this->customer_name,
                'table_number' => $this->table_number,
                // 'notes' => null,
            ]);

            foreach ($this->cart as $item) {
                $order->items()->create([
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                    'options' => $item['option_text'] ?? null,
                ]);
            }
            DB::commit();
            $this->latestOrder = $order->load(['items']);
            $this->showPaymentModal = false;
            $this->showReceipt = true;
            session()->flash('success', 'Transaksi Berhasil! No: ' . $order->invoice_number);
            $this->clearCart();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
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
            <div class="grid grid-cols-1 sm:grid-cols- md:grid-cols-6 lg:grid-cols-4 xl:grid-cols-5 gap-3"
                wire:poll.5s.visible.keep-alive>
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
        <div class="p-4 flex items-center justify-between border-b border-gray-100 bg-white">
            <div class="flex flex-col">
                <h2 class="text-sm font-black text-secondary uppercase tracking-[0.2em] leading-none">
                    Cart
                </h2>
            </div>

            <div
                class="relative flex items-center justify-center w-10 h-10 bg-primary/10 rounded-xl text-primary border border-primary/10 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                </svg>

                @if (count($cart) > 0)
                    <span
                        class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-secondary text-[10px] font-black text-white border-2 border-white shadow-md animate-in zoom-in duration-300">
                        {{ count($cart) }}
                    </span>
                @endif
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
            {{-- <div
                class="bg-white border border-gray-200 rounded-xl p-2.5 flex justify-between items-center mb-4 shadow-sm">
                <span class="text-2xs uppercase font-black text-gray-400">Add Extras</span>
                <div class="flex gap-3 text-2xs font-bold text-secondary uppercase">
                    <button class="hover:text-primary transition">Disc</button>
                    <button class="hover:text-primary transition">Coupon</button>
                    <button class="hover:text-primary transition">Note</button>
                </div>
            </div> --}}

            <div class="flex gap-3 mb-6">
                <input type="text" wire:model.live="customer_name" placeholder="Customer name"
                    class="flex-1 px-5 py-2 bg-white border border-gray-200 text-xs font-black tracking-widest  placeholder-gray-400 focus:border-amber-900 focus:ring-0 transition-all" />

                <input type="number" wire:model.live="table_number" placeholder="00"
                    class="w-24 px-2 py-2 bg-white border border-gray-200 text-xs font-black text-center tracking-widest placeholder-gray-400 focus:border-amber-900 focus:ring-0 transition-all" />
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
                <button wire:click="clearCart"
                    class="flex flex-col items-center justify-center bg-secondary text-white py-3 rounded-xl font-bold hover:bg-secondary/90 active:scale-95 transition-all text-2xs uppercase shadow-md shadow-secondary/20">
                    <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Clear cart
                </button>

                <button wire:click="openPayment" wire:loading.attr="disabled"
                    {{ empty($cart) || empty($customer_name) || empty($table_number) ? 'disabled' : '' }}
                    class="w-full flex flex-col items-center justify-center py-3 rounded-xl font-bold transition-all text-2xs uppercase shadow-md 
        {{ empty($cart) || empty($customer_name) || empty($table_number)
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed shadow-none border border-gray-200'
            : 'bg-primary text-white hover:brightness-110 active:scale-95 shadow-primary/20' }}">

                    {{-- Loading Spinner --}}
                    <div wire:loading wire:target="openPayment">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <div wire:loading.remove wire:target="openPayment" class="flex flex-col items-center">
                        <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pay Now
                    </div>
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
    @if ($showReceipt && $latestOrder)
        <div class="fixed inset-0 z-100 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div
                class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden animate-in zoom-in duration-300">
                <div id="receipt-print" class=" bg-white text-gray-800 font-mono text-sm">
                    <div class="text-center">
                        <div class="flex justify-center items-center mb-0">
                            <img src="{{ asset('images/logo-text-v2.png') }}" class="w-48 h-auto object-contain">
                        </div>
                        <p class="text-2xs text-gray-500 font-medium">Jl. Sudirman No. 123, Pekanbaru</p>
                        <div class="border-b border-dashed my-4"></div>
                        <p class="text-2xs uppercase font-bold text-gray-700">No:
                            {{ $latestOrder->invoice_number }}</p>
                        <p class="text-2xs text-gray-500" x-data="{
                            formatDate(dateString) {
                                return new Intl.DateTimeFormat('id-ID', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                }).format(new Date(dateString));
                            }
                        }">
                            <span x-text="formatDate('{{ $latestOrder->created_at->toIso8601String() }}')"></span>
                        </p>
                        <div class="text-center text-2xs text-secondary italic">
                            Cashier : {{ $currentStaff->name }}
                        </div>
                    </div>

                    <div class="px-6">
                        <div class="space-y-3 py-2">
                            @foreach ($latestOrder->items as $item)
                                <div class="flex flex-col">
                                    <div class="flex justify-between font-bold">
                                        <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
                                        <span>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($item->options)
                                        <span class="text-2xs text-gray-500 italic">-
                                            {{ $item->options }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="border-b border-dashed my-4"></div>

                        <div class="space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span
                                    class="font-bold">Rp{{ number_format($latestOrder->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax (10%)</span>
                                <span
                                    class="font-bold">Rp{{ number_format($latestOrder->tax_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Total</span>
                                <span
                                    class="font-bold">Rp{{ number_format($latestOrder->total_price, 0, ',', '.') }}</span>
                            </div>
                            {{-- Pay and Change Info  --}}
                            <div class="flex justify-between">
                                <span>Pay</span>
                                <span
                                    class="font-bold">Rp{{ number_format($latestOrder->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Change</span>
                                <span
                                    class="font-bold">Rp{{ number_format($latestOrder->change_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Order/Table</span>
                                <span class="font-bold">{{ $latestOrder->customer_name }} /
                                    {{ $latestOrder->table_number }}</span>
                            </div>
                        </div>

                        <div class="text-center mt-8 text-2xs text-secondary italic">
                            We truly appreciate your visit!<br>
                            Please note that purchased items are final and cannot be exchanged.<br>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="p-4 bg-gray-50 flex gap-3 border-t">
                    <button onclick="window.print()"
                        class="flex-1 bg-secondary text-white py-3 rounded-xl font-bold text-xs uppercase hover:bg-secondary/90 transition">
                        Print
                    </button>
                    <button wire:click="$set('showReceipt', false)"
                        class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-bold text-xs uppercase hover:bg-gray-300 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #receipt-print,
                #receipt-print * {
                    visibility: visible;
                }

                #receipt-print {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
            }
        </style>
    @endif

    @if ($showPaymentModal)
        <div class="fixed inset-0 z-60 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-black uppercase text-secondary">Payment Confirmation</h3>
                    <button wire:click="$set('showPaymentModal', false)" class="text-gray-400 hover:text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-8 space-y-6">
                    {{-- Info Total --}}
                    <div class="text-center">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Payable</p>
                        <h2 class="text-4xl font-black text-primary">Rp{{ number_format($total, 0, ',', '.') }}</h2>
                    </div>
                    @if (session()->has('error'))
                        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-sm rounded-r-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-bold uppercase text-xs text-wrap">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif
                    <hr class="border-dashed">

                    {{-- Input Uang --}}
                    <div class="space-y-2">
                        <label class="text-2xs font-black uppercase text-gray-500">Uang Diterima (Cash)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400">Rp</span>
                            <input type="number" wire:model.live="paidAmountInput"
                                class="w-full pl-12 pr-4 py-4 bg-gray-100 border-none rounded-2xl text-xl font-black focus:ring-2 focus:ring-primary transition-all">
                        </div>
                    </div>

                    {{-- Change --}}
                    <div class="bg-secondary/5 p-4 rounded-2xl flex justify-between items-center">
                        <span class="text-xs font-bold text-secondary uppercase">Change</span>
                        <span class="text-xl font-black text-secondary">
                            Rp{{ number_format($changeAmount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t">
                    @php
                        $isAmountInsufficient = $paidAmountInput < $total || $paidAmountInput <= 0;
                    @endphp

                    <button wire:click="payNow" wire:loading.attr="disabled"
                        {{ $isAmountInsufficient ? 'disabled' : '' }}
                        class="w-full py-4 rounded-2xl font-black uppercase tracking-widest transition-all flex justify-center items-center gap-2 shadow-lg
        {{ $isAmountInsufficient
            ? 'bg-gray-300 text-gray-500 cursor-not-allowed shadow-none'
            : 'bg-primary text-white hover:brightness-110 active:scale-95 shadow-primary/20' }}">

                        {{-- State: Loading --}}
                        <div wire:loading wire:target="payNow" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>

                        {{-- State: Normal / Ready --}}
                        <div wire:loading.remove wire:target="payNow" class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Complete Order</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- <div class="max-w-7xl mx-auto px-6 mt-4"> --}}
    {{-- Notifikasi Sukses --}}
    {{-- @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-sm rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-bold uppercase text-xs">{{ session('success') }}</span>
                </div>
            </div>
        @endif --}}

    {{-- Notifikasi Error
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-sm rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-bold uppercase text-xs text-wrap">{{ session('error') }}</span>
                </div>
            </div>
        @endif --}}
    {{-- </div> --}}
</div>
