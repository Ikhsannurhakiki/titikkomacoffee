<x-app-layout>
    <div class="flex h-full w-full">
        <div class="flex-1 p-6 overflow-y-auto">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p>Selamat datang kembali, {{ Auth::user()->name }}!</p>
            </div>
        </div>

        <aside class="w-96 bg-gray-50 border-l invisible lg:block shrink-0"></aside>
    </div>
</x-app-layout>
''
