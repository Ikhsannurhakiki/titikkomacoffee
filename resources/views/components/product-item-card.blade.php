@props(['product'])

<div @if ($product->is_available) wire:click="openCustomizer({{ $product->id }})" @endif
    wire:loading.attr="disabled"
    {{ $attributes->merge([
        'class' =>
            'group relative bg-white p-2 rounded-lg shadow-sm border transition-all duration-200 ' .
            ($product->is_available
                ? 'border-gray-100 cursor-pointer hover:border-primary/40 hover:shadow-md hover:-translate-y-1 active:scale-95'
                : 'border-gray-200 opacity-60 cursor-not-allowed'),
    ]) }}>

    {{-- Loading Spinner Overlay --}}
    <div wire:loading wire:target="openCustomizer({{ $product->id }})"
        class="absolute inset-0 bg-white/60 z-20 flex items-center justify-center rounded-lg">
        <svg class="animate-spin h-6 w-6 text-primary" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
        </svg>
    </div>

    <div class="relative mb-2 aspect-square overflow-hidden rounded-lg group">
        <img src="{{ $product->getFirstMediaUrl('thumbnail') ?: asset('images/logo.png') }}"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
            style="{{ !$product->is_available ? 'filter: grayscale(100%) brightness(0.7);' : '' }}"
            alt="{{ $product->name }}">

        @if (!$product->is_available)
            <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                <span
                    class="bg-red-500 text-white text-2xs px-2 py-1 rounded shadow-lg font-black uppercase tracking-wider">
                    Out of Stock
                </span>
            </div>
        @endif
    </div>

    <div class="space-y-1">
        <h3 class="text-xs font-bold {{ $product->is_available ? 'text-gray-800' : 'text-gray-400' }} truncate">
            {{ $product->name }}
        </h3>

        <div class="flex items-center justify-between pt-1 border-t border-gray-50 mt-2">
            <span class="text-xs font-black {{ $product->is_available ? 'text-secondary' : 'text-gray-400' }}">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </span>

            {{-- Icon Indikator --}}
            <div class="p-1 {{ $product->is_available ? 'text-primary' : 'text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
        </div>
    </div>
</div>
