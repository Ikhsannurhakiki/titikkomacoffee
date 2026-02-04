<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

new class extends Component {
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function with()
    {
        $orders = Order::query()
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', 'completed') // Sesuaikan status order Anda
            ->latest()
            ->get();

        return [
            'orders' => $orders,
            'totalRevenue' => $orders->sum('total_price'),
            'totalOrders' => $orders->count(),
            'averageTicket' => $orders->count() > 0 ? $orders->sum('total_price') / $orders->count() : 0,
        ];
    }

    public function exportPDF()
    {
        $orders = Order::query()
            ->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        $data = [
            'orders' => $orders,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalRevenue' => $orders->sum('total_price'),
        ];

        // Mengarah ke file blade khusus PDF
        $pdf = Pdf::loadView('pdf.sales-report', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan-Penjualan-' . $this->startDate . '.pdf');
    }
}; ?>

<div class="flex h-screen w-full overflow-hidden bg-gray-50/50">
    <div class="flex-1 overflow-y-auto min-w-0">
        {{-- Header --}}
        <x-header title="Sales Report">
            <div class="flex items-center gap-2 bg-white p-1 rounded-3xl shadow-sm border border-gray-100">
                <input type="date" wire:model.live="startDate"
                    class="border-none text-xs font-bold text-primary focus:ring-0 rounded-xl">
                <span class="text-gray-300">to</span>
                <input type="date" wire:model.live="endDate"
                    class="border-none text-xs font-bold text-primary focus:ring-0 rounded-xl">
                <button wire:click="exportPDF"
                    class="flex items-center gap-2 bg-primary text-white px-5 py-2 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-amber-900 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </x-header>
        {{-- Header & Filter --}}

        <div class="p-6">
            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Total Pendapatan --}}
                <div
                    class="bg-primary p-6 rounded-[2.5rem] text-white shadow-xl shadow-primary/20 relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Total Penjualan</p>
                        <h2 class="text-3xl font-black mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                    </div>
                    <svg class="absolute -right-4 -bottom-4 w-32 h-32 opacity-10" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z" />
                    </svg>
                </div>

                {{-- Total Transaksi --}}
                <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Jumlah Order</p>
                    <h2 class="text-3xl font-black text-primary mt-1">{{ $totalOrders }} <span
                            class="text-sm font-bold opacity-50">Transaksi</span></h2>
                </div>

                {{-- Rata-rata Per Transaksi --}}
                <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest">Rata-rata Penjualan</p>
                    <h2 class="text-3xl font-black text-primary mt-1">Rp
                        {{ number_format($averageTicket, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            {{-- Tabel Transaksi --}}
            <div class="bg-white rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-black text-primary uppercase tracking-widest text-sm">Riwayat Transaksi Terakhir
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-amber-900 uppercase">Invoice</th>
                                <th class="px-6 py-4 text-[10px] font-black text-amber-900 uppercase">Waktu</th>
                                <th class="px-6 py-4 text-[10px] font-black text-amber-900 uppercase">Staff</th>
                                <th class="px-6 py-4 text-[10px] font-black text-amber-900 uppercase text-right">Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orders as $order)
                                <tr class="hover:bg-amber-50/30 transition-colors">
                                    <td class="px-6 py-4 font-black text-primary text-xs">#{{ $order->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 text-[11px] text-gray-500 font-bold">
                                        {{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 bg-amber-100 text-amber-900 text-[9px] font-black rounded-full uppercase">{{ $order->staff->name ?? 'Admin' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-primary text-xs">Rp
                                        {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-10 text-center text-gray-400 font-bold italic text-sm">
                                        Tidak ada transaksi ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
