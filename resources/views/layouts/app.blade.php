    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Titik Koma POS') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased bg-gray-100 overflow-hidden">
        <div class="flex h-screen w-full">
            {{-- Kolom 1: Sidebar Kiri (Tetap) --}}
            <aside class="w-64 bg-white border-r shrink-0">
                @include('components.sidebar')
            </aside>

            {{-- Kolom 2: Area Konten (Dinamis) --}}
            {{-- Hapus semua padding/overflow di sini agar diatur oleh isi $slot --}}
            <main id="main-content" class="flex-1 flex overflow-hidden">
                {{ $slot }}
            </main>
        </div>
    </body>

    </html>
