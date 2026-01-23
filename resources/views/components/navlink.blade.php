@props([
    'href' => '#',
    'title' => 'Dashboard',
    'active' => false,
])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' =>
            'flex flex-col items-center justify-center w-20 aspect-square p-2 text-xs font-normal rounded-lg group transition-all duration-200 border-2 ' .
            ($active
                ? 'border-primary bg-gray-100 dark:bg-secondary/20'
                : 'border-transparent hover:border-primary hover:bg-gray-100 dark:hover:bg-secondary/20'),
    ]) }}>

    <div class="text-primary group-hover:text-primary-light transition duration-75">
        {{ $slot }}
    </div>

    <span
        class="mt-1.5 font-bold text-primary group-hover:text-primary-light text-center leading-tight text-subtitle transition duration-75">
        {{ $title }}
    </span>
</a>
