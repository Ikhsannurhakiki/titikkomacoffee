{{-- <x-app-layout> --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Sidebar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    {{-- <div class="flex-1 ml-64">
        <header class="h-16 bg-white border-b flex items-center px-8 shadow-sm">
            <div class="flex justify-center gap-4">
                <h2 class="text-xl font-semibold text-gray-800">Halaman Dashboard</h2>
            </div>
        </header>

        <main class="p-8">
            @yield('content')
        </main>

    </div>

    <x-sidebar /> --}}

    <div class="grid-container">
        <aside class="sidebar">
            @include('components.sidebar')
        </aside>

        <main class="content">
            @yield('content')
        </main>

        <aside class="orders">
            @include('orders')
        </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</body>

</html>
{{-- </x-app-layout> --}}
