<?php

use App\Models\Order;
use function Livewire\Volt\{with};

with(
    fn() => [
        'recentOrders' => Order::latest()->whereDate('created_at', now('Asia/Jakarta'))->take(5)->get(),
    ],
);
?>

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden']) }}
    wire:ignore>
    <div class="p-6 border-b bg-primary/15 border-primary/25 flex justify-between items-center">
        <h3 class="font-bold text-primary uppercase text-xs tracking-wider">Recent Orders</h3>
        <a href="{{ route('dashboard') }}" wire:navigate class="text-primary text-2xs font-black hover:underline">
            Details
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-primary/80">
                <tr class="text-center text-gray-200 uppercase text-2xs tracking-widest">
                    <th class="px-6 py-3 font-semibold">Invoice</th>
                    <th class="px-6 py-3 font-semibold">Total</th>
                    <th class="px-6 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-center">
                @forelse ($recentOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition group">
                        <td class="px-6 py-4 font-bold text-gray-400 group-hover:text-gray-600">
                            #{{ $order->invoice_number }}
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'completed' => 'bg-green-100 text-green-600',
                                    'processing' => 'bg-yellow-100 text-yellow-600',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    'pending' => 'bg-blue-100 text-blue-600',
                                ];
                                $class = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="px-2 py-1 rounded-md text-[9px] font-black uppercase {{ $class }}">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                            Belum ada transaksi saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
