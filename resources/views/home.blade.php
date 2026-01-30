<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-black text-secondary tracking-tighter mb-2">TITIK K<span class="text-primary">O</span>MA
        </h1>
        <p class="text-gray-500 font-medium">Silakan pilih akses panel Anda</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl w-full">

        <a href="{{ route('dashboard') }}"
            class="group relative bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/10 hover:-translate-y-2 overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z">
                    </path>
                </svg>
            </div>
            <div class="relative z-10">
                <div
                    class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-secondary mb-2">Admin</h2>
                <p class="text-sm text-gray-500 leading-relaxed">Kelola produk, stok, laporan keuangan, dan manajemen
                    staff.</p>
            </div>
        </a>

        <a href="{{ route('dashboard') }}"
            class="group relative bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-500/10 hover:-translate-y-2 overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                    <path fill-rule="evenodd"
                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                        clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <div
                    class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-secondary mb-2">Cashier</h2>
                <p class="text-sm text-gray-500 leading-relaxed">Input pesanan pelanggan, proses pembayaran, dan cetak
                    struk.</p>
            </div>
        </a>

        <a href="{{ route('dashboard') }}"
            class="group relative bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:shadow-green-500/10 hover:-translate-y-2 overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-2 0v1h2zm3 0V5a1 1 0 10-2 0v1h2z"
                        clip-rule="evenodd"></path>
                    <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"></path>
                </svg>
            </div>
            <div class="relative z-10">
                <div
                    class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-secondary mb-2">Kitchen</h2>
                <p class="text-sm text-gray-500 leading-relaxed">Pantau antrean pesanan, mulai proses masak, dan tandai
                    pesanan siap.</p>
            </div>
        </a>

    </div>

    <p class="mt-12 text-gray-400 text-xs font-bold uppercase tracking-[0.2em]">Titik Koma Coffee v2.0</p>
</div>
