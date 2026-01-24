@props(['product'])

<div {{ $attributes }}
    class="group bg-white p-2 rounded-lg shadow-sm border border-gray-100 hover:border-primary/40 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">

    <div class="relative mb-2 aspect-square overflow-hidden rounded-lg">
        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/dummy-item.png') }}"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

        @if ($product->stock <= 5)
            <span class="absolute top-2 right-2 bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-md font-bold">
                Stok Tipis
            </span>
        @endif
    </div>

    <div class="space-y-1">
        <h3 class="text-xs font-bold text-gray-800 truncate">{{ $product->name }}</h3>

        <div class="flex items-center justify-between pt-1 border-t border-gray-50 mt-2">
            <span class="text-xs font-black text-secondary">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </span>
            <button class="p-1 bg-gray-50 rounded-md group-hover:bg-primary group-hover:text-white transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>
</div>
