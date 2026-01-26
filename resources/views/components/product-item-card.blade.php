@props(['product'])

<div
    {{ $attributes->merge([
        'class' =>
            'group bg-white p-2 rounded-lg shadow-sm border transition-all duration-200 ' .
            ($product->is_available
                ? 'border-gray-100 cursor-pointer hover:border-primary/40 hover:shadow-md hover:-translate-y-1'
                : 'border-gray-200 opacity-80 cursor-not-allowed'),
    ]) }}>

    <div class="relative mb-2 aspect-square overflow-hidden rounded-lg group">
        {{-- Gambar dengan efek grayscale jika habis --}}
        <img src="{{ $product->getFirstMediaUrl('thumbnail') ?: asset('images/logo.png') }}"
            class="w-full h-full object-cover transition-transform duration-300"
            style="{{ !$product->is_available ? 'filter: grayscale(75%) brightness(0.6);' : '' }}"
            alt="{{ $product->name }}">

        {{-- Overlay & Badge --}}
        @if (!$product->is_available)
            {{-- Overlay gelap tambahan agar gambar lebih redup --}}
            <div class="absolute inset-0 bg-black/20"></div>

            <span
                class="absolute top-2 right-2 bg-gray-500 text-white text-[10px] px-2 py-1 rounded shadow-lg font-black uppercase tracking-wider">
                Out of Stock
            </span>

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <svg class="w-10 h-10 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        @endif
    </div>

    <div class="space-y-1">
        <h3 class="text-xs font-bold text-gray-800 truncate"
            style="{{ !$product->is_available ? 'color: #9ca3af;' : '' }}">{{ $product->name }}</h3>

        <div class="flex items-center justify-between pt-1 border-t border-gray-50 mt-2">
            <span class="text-xs font-black text-secondary"
                style="{{ !$product->is_available ? 'color: #9ca3af;' : '' }}">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </span>
            <button class="p-1 bg-gray-50 rounded-md "
                style="{{ $product->is_available ? 'group-hover:bg-primary group-hover:text-white transition-colors' : '' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>
</div>
