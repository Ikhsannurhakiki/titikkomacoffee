@props(['title', 'value', 'iconColor' => 'green'])

@php
    $colors = [
        'primary' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-600', 'ring' => 'ring-primary-100'],
        'secondary' => ['bg' => 'bg-secondary-50', 'text' => 'text-primary', 'ring' => 'ring-secondary-100'],
        'gray' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'ring' => 'ring-gray-100'],
        'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'ring' => 'ring-green-100'],
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'ring-blue-100'],
        'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'ring' => 'ring-orange-100'],
        'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'ring' => 'ring-purple-100'],
    ][$iconColor] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'ring' => 'ring-gray-100'];
@endphp

<div
    {{ $attributes->merge(['class' => 'group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center relative overflow-hidden transition-all hover:shadow-md hover:-translate-y-1']) }}>
    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-secondary opacity-80"></div>
    <div
        class="p-3 {{ $colors['bg'] }} {{ $colors['ring'] }} rounded-xl mr-4 ring-4 group-hover:scale-110 transition-transform duration-300">
        <div class="w-6 h-6 {{ $colors['text'] }}">
            {{ $slot }}
        </div>
    </div>

    <div class="relative flex flex-col justify-center self-center">
        <p class="text-2xs font-black text-gray-400 uppercase tracking-widest mb-1.5 leading-tight">
            {{ $title }}
        </p>
        <h2 class="text-2xl font-black text-secondary leading-tight tracking-tight">
            {{ $value }}
        </h2>
    </div>
</div>
