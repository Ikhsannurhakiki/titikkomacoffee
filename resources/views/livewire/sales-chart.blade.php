<?php

use App\Models\Order;
use Carbon\Carbon;
use function Livewire\Volt\{with};

with(
    fn() => [
        'chartData' => collect(range(6, 0))
            ->mapWithKeys(function ($i) {
                $date = now('Asia/Jakarta')->subDays($i);
                return [$date->format('D') => 0];
            })
            ->merge(
                Order::where('status', 'completed')
                    ->where('created_at', '>=', now('Asia/Jakarta')->subDays(6)->startOfDay())
                    ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
                    ->groupBy('date')
                    ->get()
                    ->mapWithKeys(fn($item) => [Carbon::parse($item->date)->format('D') => (int) $item->total]),
            ),
    ],
);
?>

<div {{ $attributes->merge(['class' => 'bg-white p-6 rounded-2xl shadow-sm border border-gray-100']) }} wire:ignore>
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-primary uppercase text-xs tracking-wider">Earnings Trend (7 Days)</h3>

    </div>

    <div class="h-64">
        <canvas id="salesChartCanvas"></canvas>
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            renderChart();
        });

        document.addEventListener('livewire:load', () => {
            Livewire.on('el-updated', () => renderChart());
        });

        function renderChart() {
            const ctx = document.getElementById('salesChartCanvas');
            if (!ctx) return;

            const data = @json($chartData);

            const existingChart = Chart.getChart(ctx);
            if (existingChart) existingChart.destroy();

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        borderColor: '#6F4E37',
                        backgroundColor: 'rgba(111, 78, 55, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'Rp ' + value.toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        }
        renderChart();
    </script>
</div>
