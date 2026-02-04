@props([
    'href' => '#',
    'title' => 'Dashboard',
    'active' => false,
])

<a href="{{ $href }}" wire:navigate
    {{ $attributes->merge([
        'class' =>
            'flex flex-row items-center gap-3 w-full px-4 py-3 text-sm rounded-xl group ' .
            'transition-all duration-300 ease-in-out border-2 ' .
            ($active
                ? 'border-primary bg-secondary shadow-md translate-x-1 text-white shadow-secondary/20'
                : 'border-transparent text-primary hover:border-primary/20 hover:bg-gray-100 hover:translate-x-1 dark:hover:bg-secondary/20'),
    ]) }}>

    {{-- Icon: Warna berubah jadi putih jika active, jika tidak gunakan text-primary --}}
    <div
        class="transition-transform duration-300 ease-out group-hover:scale-110 shrink-0 
        {{ $active ? 'text-white' : 'text-primary' }}">
        {{ $slot }}
    </div>

    {{-- Text: Secara otomatis akan mengikuti text-white dari parent (tag <a>) karena tidak kita kunci warnanya --}}
    <span class="font-bold transition-colors duration-300">
        {{ $title }}
    </span>
</a>
