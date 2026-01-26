@props([
    'placeholder' => 'Cari...',
    'model' => 'search',
])

<div {{ $attributes->merge(['class' => 'relative group w-72 flex items-center']) }}>
    {{-- Icon Search --}}
    {{-- Kita gunakan inset-y-0 agar div ikon mengikuti tinggi penuh input, lalu flex untuk centering --}}
    <div class="relative w-72">
        <input type="text" wire:model.live="{{ $model }}" placeholder="{{ $placeholder }}"
            class="w-full pl-10 pr-4 py-2 rounded-xl border-none ring-1 ring-secondary focus:ring-2 focus:ring-primary transition-all shadow-sm text-sm font-medium">
        <svg class="w-4 h-4 absolute left-3 top-3 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    {{-- <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="w-4 h-4 text-gray-400 group-focus-within:rotate-90 group-focus-within:text-blue-500 transition-transform duration-500"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div> --}}

    {{-- Input Field --}}
    {{-- <input type="text" wire:model.live="{{ $model }}" placeholder="{{ $placeholder }}"
        class="w-full pl-10 pr-4 py-2 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 transition-all shadow-sm text-sm font-medium bg-white"> --}}

</div>
