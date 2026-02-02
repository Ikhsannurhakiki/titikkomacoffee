<?php

use App\Models\Order;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = '';

    // Reset pagination saat search/status berubah
    public function updating($property)
    {
        if (in_array($property, ['search', 'status'])) {
            $this->resetPage();
        }
    }

    public function with()
    {
        return [
            'orders' => Order::query()->when($this->search, fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%"))->when($this->status, fn($q) => $q->where('status', $this->status))->latest()->paginate(7),
        ];
    }
}; ?>

<div class="h-screen flex flex-col bg-gray-50 overflow-hidden">
    {{-- Header Tetap (Sticky otomatis karena komponen x-header kita sudah diset sticky) --}}
    <x-header title="Order List" subtitle="Kelola semua riwayat transaksi">

        <x-search-bar placeholder="Cari nomor invoice..." />

        <select wire:model.live="status"
            class=" w-full text-left pr-10 py-2 rounded-xl border-none ring-1 ring-secondary focus:ring-2 focus:ring-primary transition-all shadow-sm text-sm font-medium">
            <option value="">All</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </x-header>

    {{-- Area Tabel yang Bisa Di-scroll --}}
    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <x-table :headers="['Date', 'Invoice', 'Total', 'Status', 'Action']">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span
                                class="text-xs font-bold text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                            <p class="text-[10px] text-gray-400">{{ $order->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="text-sm font-black text-primary group-hover:underline">#{{ $order->invoice_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-800">Rp
                                {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
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
                                {{-- View Detail --}}
                                <button
                                    class="p-2 text-gray-400 hover:text-primary transition-colors bg-gray-50 rounded-lg hover:bg-primary/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                {{-- Print/Action --}}
                                <button
                                    class="p-2 text-gray-400 hover:text-secondary transition-colors bg-gray-50 rounded-lg hover:bg-secondary/10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2.458L17 15.208V17zM17 11V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2v-4a2 2 0 00-2-2h-7l-2-2H5z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center italic text-gray-400">
                                <span class="text-4xl mb-2">📦</span>
                                <p class="text-sm font-bold">Tidak ada pesanan ditemukan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                <x-slot name="pagination">
                    {{ $orders->links('vendor.pagination.tailwind') }}
                </x-slot>
            </x-table>
        </div>
    </div>
</div>
