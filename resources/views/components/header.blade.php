@props([
    'title' => 'Title',
    'subtitle' => 'Subtitle',
])

<div
    {{ $attributes->merge(['class' => 'sticky top-0 z-[100] flex justify-between mb-6 items-center bg-white/80 backdrop-blur-md py-3.5 px-8 border-b border-gray-100 shadow-sm']) }}>
    <div>
        <h1 class="text-2xl font-black text-secondary tracking-tight">{{ $title }}</h1>
    </div>

    <div class="flex items-center space-x-2">
        {{ $slot }}
    </div>

    <div class="flex items-center space-x-4">
        <div class="flex flex-col font-bold items-end text-xs mr-6">
            <span class="text-primary uppercase tracking-tighter">
                {{ now()->isoFormat('dddd, D MMMM YYYY') }}
            </span>
            <span wire:ignore id="clock" class="text-secondary font-black"></span>
        </div>

        <button wire:click="$refresh"
            class="p-2.5 bg-secondary text-white rounded-xl transition-all duration-300 shadow-md hover:shadow-secondary/20 group active:scale-95">
            <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-700" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
        </button>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('clock').innerText = time;
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
