<?php

use App\Models\Order;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = '';
    public $selectedOrder = null;
    public $showModal = false;

    public function updating($property)
    {
        if (in_array($property, ['search', 'status'])) {
            $this->resetPage();
        }
    }

    public function with()
    {
        return [
            'orders' => Order::query()->when($this->search, fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))->when($this->status, fn($q) => $q->where('status', $this->status))->latest()->paginate(7)->onEachSide(1),
        ];
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with('items')->find($orderId);
        $this->showModal = true;
    }

    public function completeOrder()
    {
        if ($this->selectedOrder) {
            $this->selectedOrder->update(['status' => 'completed']);
            $this->showModal = false;
            $this->dispatch('orderUpdated');
        }
    }
}; ?>

<div class="h-screen flex flex-col bg-gray-50 overflow-hidden relative">
    {{-- Header --}}
    <x-header title="Order List" subtitle="Kelola semua riwayat transaksi">
        <x-search-bar placeholder="Cari nomor invoice..." />

        <select wire:model.live="status"
            class="w-full text-left pr-10 py-2 rounded-xl border-none ring-1 ring-secondary focus:ring-2 focus:ring-primary transition-all shadow-sm text-sm font-medium">
            <option value="">All Status</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </x-header>

    {{-- Table Area --}}
    <div class="flex-1 overflow-y-auto p-6" wire:poll.10s.visible.keep-alive>
        <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <x-table :headers="['Date', 'Invoice', 'Total', 'Status', 'Action']">
                @forelse($orders as $order)
                    <tr wire:click="viewOrder({{ $order->id }})" class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span
                                class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                            <p class="text-[10px] text-gray-400">{{ $order->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-black text-primary group-hover:underline uppercase italic">
                                #{{ $order->invoice_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter
                                {{ $order->status === 'completed'
                                    ? 'bg-green-100 text-green-600'
                                    : ($order->status === 'cancelled'
                                        ? 'bg-red-100 text-red-600'
                                        : 'bg-yellow-100 text-yellow-600') }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="viewOrder({{ $order->id }})"
                                    class="p-2 text-gray-400 hover:text-primary transition-all bg-gray-50 rounded-lg hover:bg-primary/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                                        viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <p class="text-gray-400 italic font-bold">Tidak ada pesanan ditemukan 📦</p>
                        </td>
                    </tr>
                @endforelse

                <x-slot name="pagination">
                    {{ $orders->links('vendor.pagination.tailwind') }}
                </x-slot>
            </x-table>
        </div>
    </div>

    {{-- MODAL DETAIL - Perbaikan Z-Index & Blur --}}
    @if ($showModal && $selectedOrder)
        <div class="fixed inset-0 z-[200] overflow-y-auto" x-data x-init="document.body.style.overflow = 'hidden'"
            x-on:detach.window="document.body.style.overflow = 'auto'">

            <div class="flex items-center justify-center min-h-screen p-4">
                {{-- Overlay --}}
                <div class="fixed inset-0 bg-secondary/40 backdrop-blur-md transition-opacity"
                    wire:click="$set('showModal', false)"></div>

                {{-- Modal Content --}}
                <div
                    class="relative bg-white rounded-[2.5rem] w-full max-w-lg p-8 shadow-2xl transition-all scale-100 opacity-100">

                    {{-- Header Modal --}}
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-black text-secondary uppercase tracking-tighter">
                                {{ $selectedOrder->customer_name }} • Meja {{ $selectedOrder->table_number }}
                            </h3>
                            <p class="text-sm font-bold text-primary uppercase mt-1">
                                Order #{{ $selectedOrder->invoice_number }}
                            </p>
                        </div>
                        <button wire:click="$set('showModal', false)"
                            class="p-2 bg-gray-50 rounded-full text-gray-400 hover:text-red-500 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- List Item --}}
                    <div class="space-y-4 mb-8 max-h-[350px] overflow-y-auto pr-2">
                        <p
                            class="text-[10px] font-black text-amber-900/40 uppercase tracking-widest border-b border-gray-100 pb-2">
                            Order Items
                        </p>

                        @foreach ($selectedOrder->items as $item)
                            <div class="flex justify-between items-start">
                                <div class="flex gap-3">
                                    <div
                                        class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center text-xs font-black text-primary">
                                        {{ $item->quantity }}x
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-secondary">{{ $item->product_name }}</p>
                                        @if ($item->options)
                                            <p class="text-[11px] text-amber-700 font-medium italic mt-0.5">—
                                                {{ $item->options }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-gray-500">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Action Button --}}
                    @if ($canManageOrder)
                        <div class="pt-4 border-t border-gray-100">
                            @if ($selectedOrder->status === 'processing')
                                <button wire:click="completeOrder"
                                    class="w-full bg-primary text-white py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-primary/20 hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3"
                                        viewBox="0 0 24 24">
                                        <path d="M5 13l4 4L19 7" />
                                    </svg>
                                    Mark as Completed
                                </button>
                            @else
                                <div
                                    class="w-full bg-green-50 text-green-600 py-4 rounded-2xl font-black text-xs uppercase text-center border border-green-100 italic">
                                    ✨ Order Completed
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
