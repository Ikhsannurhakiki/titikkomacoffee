@props([
    'title' => '',
])

<button
    {{ $attributes->merge([
        'class' =>
            'group relative bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] hover:shadow-[0_20px_50px_-10px_rgba(120,66,18,0.15)] transition-all duration-500 hover:-translate-y-2 overflow-hidden',
    ]) }}>
    <div
        class="absolute -right-6 -top-6 w-24 h-24 bg-amber-900/5 rounded-full blur-3xl group-hover:bg-amber-900/10 transition-colors">
    </div>

    <div class="flex flex-col items-center text-center space-y-5 relative z-10">
        <div
            class="p-5 bg-amber-900 rounded-[1.5rem] shadow-xl shadow-amber-900/30 group-hover:rotate-6 transition-transform duration-500">
            {{ $icon }}
        </div>

        <div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tight">
                {{ $title }}
            </h3>
        </div>
    </div>
</button>
