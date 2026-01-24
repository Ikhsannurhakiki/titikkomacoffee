@props([
    'qty' => 1,
    'name' => '',
    'price' => 0,
    'image' => 'public/images/dummy-item.png', // Default placeholder
    'originalPrice' => null,
    'description' => null,
    'expanded' => false,
    'discount' => 0,
])

<div
    {{ $attributes->merge([
        'class' =>
            'transition-all duration-300 w-full ' .
            ($expanded
                ? 'bg-white rounded-2xl border border-gray-100 p-4 shadow-lg ring-1 ring-black/5 mb-2'
                : 'flex items-center justify-between py-4 border-b border-gray-50 hover:bg-gray-50/50 px-2 rounded-lg'),
    ]) }}>

    <div class="flex items-center gap-3 min-w-0">
        <div class="relative shrink-0">
            <img src="{{ asset('images/dummy-item.png') }}" alt="{{ $name }}"
                class="w-12 h-12 rounded-xl object-cover shadow-sm border border-secondary-100">
            <span
                class="absolute -top-2 -right-2  text-white text-2xs font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-third-light bg-secondary shadow-sm">
                {{ $qty }}
            </span>
        </div>

        <div class="min-w-0 ml-1">
            <p
                class="text-[14px] font-bold text-gray-800 truncate leading-tight group-hover:text-primary transition-colors">
                {{ $name }}
            </p>
            @if ($description)
                <p class="text-[11px] text-gray-400 font-medium truncate mt-0.5">{{ $description }}</p>
            @endif

            @if ($expanded)
                <span class="inline-block mt-1 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-md">
                    In Stock
                </span>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3 shrink-0 ml-2">
        <div class="text-right">
            <p class="text-[14px] font-extrabold text-gray-900">${{ number_format($price, 2) }}</p>
            @if ($originalPrice)
                <p class="text-[10px] text-gray-400 line-through decoration-red-400/50">
                    ${{ number_format($originalPrice, 2) }}</p>
            @endif
        </div>

        <div class="flex flex-col gap-1">
            <button class="text-gray-300 hover:text-red-500 transition-colors p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>

    @if ($expanded)
        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-50">
            <div class="space-y-1">
                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider ml-1">Quantity</label>
                <div class="flex items-center bg-gray-50 rounded-xl p-1 border border-gray-100">
                    <button
                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-white rounded-lg shadow-sm transition">-</button>
                    <input type="number" value="{{ $qty }}"
                        class="w-full bg-transparent text-center text-sm font-bold text-gray-700 outline-none">
                    <button
                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-white rounded-lg shadow-sm transition">+</button>
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider ml-1">Disc (%)</label>
                <div class="relative">
                    <input type="number" value="{{ $discount }}"
                        class="w-full bg-gray-50 border border-gray-100 rounded-xl p-2.5 text-right focus:ring-2 focus:ring-primary/10 focus:border-primary outline-none text-sm font-bold text-gray-700">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">%</span>
                </div>
            </div>
        </div>

        <button
            class="w-full mt-3 py-2 text-[11px] font-bold text-secondary bg-secondary/5 rounded-lg border border-dashed border-secondary/20 hover:bg-secondary/10 transition">
            + Add Special Instruction
        </button>
    @endif
</div>
