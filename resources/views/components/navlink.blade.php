@props([
    'href' => '#',
    'title' => 'Dashboard',
    'active' => false,
])

<a href="{{ $href }}" wire:navigate
    {{ $attributes->merge([
        'class' =>
            'flex flex-row items-center gap-3 w-full px-4 py-3 text-sm rounded-xl group ' .
            'transition-all duration-300 ease-in-out border-2 ' . // Base transition
            ($active
                ? 'border-primary bg-primary/5 shadow-sm translate-x-1'
                : 'border-transparent hover:border-primary/20 hover:bg-gray-100 hover:translate-x-1 dark:hover:bg-secondary/20'),
    ]) }}>

    {{-- Icon with scale transition --}}
    <div class="text-primary transition-transform duration-300 ease-out group-hover:scale-110 shrink-0">
        {{ $slot }}
    </div>

    {{-- Text with color transition --}}
    <span class="font-bold text-gray-700 transition-colors duration-300 group-hover:text-primary leading-tight">
        {{ $title }}
    </span>
</a>
