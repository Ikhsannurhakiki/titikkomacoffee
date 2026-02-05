<?php

use Livewire\Volt\Component;
use App\Models\Staff;
use App\Models\Attendance;

new class extends Component {
    public $pin = '';
    public $showClockOutModal = false;
    public $isSuccess = false;

    public function with()
    {
        $sessionStaff = session('current_staff');
        return [
            'currentStaff' => $sessionStaff ? Staff::find($sessionStaff['id']) : null,
        ];
    }

    public function processClockOut()
    {
        $sessionStaff = session('current_staff');
        $staff = Staff::find($sessionStaff['id'] ?? null);

        if ($staff && $staff->pin === $this->pin) {
            $now = now('Asia/Jakarta');
            $attendance = Attendance::where('staff_id', $staff->id)->whereNull('clock_out')->latest()->first();

            if ($attendance) {
                $duration = (int) \Carbon\Carbon::parse($attendance->clock_in)->diffInMinutes($now);
                $attendance->update([
                    'clock_out' => $now,
                    'duration_minutes' => $duration,
                    'status' => 'completed',
                ]);
            }

            $this->isSuccess = true;

            $this->dispatch('start-clockout-redirect');
            return;
        }

        $this->addError('pin', 'PIN Salah!');
        $this->pin = '';
    }

    public function finalLogout()
    {
        session()->forget('current_staff');
        return redirect()->route('role-login');
    }

    public function openModal()
    {
        $this->resetErrorBag();
        $this->pin = '';
        $this->showClockOutModal = true;
    }
}; ?>

<div x-data="{ sidebarOpen: false }">
    {{-- Trigger Mobile --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="inline-flex items-center p-2 mt-2 ml-3 text-sm text-primary rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
            </path>
        </svg>
    </button>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 z-40 w-45 h-screen transition-transform sm:translate-x-0 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.15)] overflow-hidden flex flex-col">

        <div class="flex items-center justify-center p-1 h-15 shrink-0">
            <img src="{{ asset('images/logo-text-v2.png') }}" alt="Logo" class="h-30 w-auto object-contain">
        </div>

        <div class="pt-2 mt-2 space-y-2 border-t border-gray-100"></div>

        <div class="flex-1 overflow-y-auto py-5 px-3">
            <ul class="space-y-2">
                <li>
                    <x-navlink href="{{ route('dashboard') }}" title="Dashboard" :active="request()->routeIs('dashboard')">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                    </x-navlink>
                </li>

                {{-- Admin & Cashier Only --}}
                @if (in_array($currentStaff?->position, ['cashier']))
                    <li>
                        <x-navlink href="{{ route('pos') }}" :active="request()->routeIs('pos')" wire:navigate title="Cashier">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2-2v14a2 2 0 002 2z" />
                            </svg>
                        </x-navlink>
                    </li>
                @endif

                <li>
                    <x-navlink href="/order-list" title="Order List" :active="request()->is('order-list*')">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </x-navlink>
                </li>

                {{-- <li>
                    <x-navlink href="/customers" title="Customers" :active="request()->is('customers*')">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                            </path>
                        </svg>
                    </x-navlink>
                </li> --}}
            </ul>
            @if ($isAdmin || $isKitchen)
                <ul class="pt-5 mt-5 space-y-2 border-t border-secondary">
                    <li>
                        <x-navlink href="{{ route('product-manager') }}" :active="request()->routeIs('product-manager')" wire:navigate
                            title="Menu Items">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 8h1a4 4 0 010 8h-1M2 8a2 2 0 012-2h11a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8z" />
                            </svg>
                        </x-navlink>
                    </li>
                    @if ($isAdmin)
                        <li>
                            <x-navlink href="/staff-manager" title="Staffs" :active="request()->is('staff-manager*')">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 00-7 7v1h12v-1a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                            </x-navlink>
                        </li>
                        <li>
                            <x-navlink href="/reports" title="Sales Report" :active="request()->is('report*')">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </x-navlink>
                        </li>
                    @endif
                </ul>
            @endif
            <ul>
                <li class="pt-5 mt-5 space-y-2 border-t border-secondary">
                    <div class="flex justify-center gap-4">
                        <x-navlink href="/attendance" title="Attendance" :active="request()->is('attendance*')">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.5 2a1 1 0 011-1h5a1 1 0 110 2h-5a1 1 0 01-1-1zm1-4a1 1 0 100 2h4a1 1 0 100-2h-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </x-navlink>
                    </div>
                </li>
            </ul>
        </div>

        {{-- Footer Sidebar dengan Info Staff & Modal --}}
        <div class="pt-5 mt-5 border-t border-gray-200 dark:border-gray-700" x-data="{ showMenu: false }">
            @if ($currentStaff || auth()->check())
                <div class="p-4 bg-gray-50/50 rounded-[2rem] mx-2 transition-all duration-300"
                    :class="showMenu ? 'bg-white shadow-sm ring-1 ring-black/5' : ''">

                    {{-- Bagian Profil yang Bisa Diklik --}}
                    <button @click="showMenu = !showMenu" class="flex items-center w-full p-1 group focus:outline-none">
                        <div
                            class="w-10 h-10 {{ $currentStaff ? 'bg-amber-900' : 'bg-indigo-700' }} rounded-xl flex items-center justify-center text-white font-black shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                            {{ substr($currentStaff ? $currentStaff->name : auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="ml-3 overflow-hidden text-left flex-1">
                            <p class="text-[11px] font-black text-gray-900 truncate uppercase tracking-tighter">
                                {{ $currentStaff ? $currentStaff->name : auth()->user()->name }}
                            </p>
                            <p class="text-[9px] text-amber-700 font-bold uppercase italic">
                                {{ $currentStaff ? $currentStaff->position : 'Administrator' }}
                            </p>
                        </div>
                        {{-- Icon Panah Indikator --}}
                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-300"
                            :class="showMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Menu yang Muncul Saat Diklik --}}
                    <div x-show="showMenu" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="mt-4 space-y-1 overflow-hidden">

                        {{-- Link Edit Profile --}}
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-4 py-2 text-[10px] font-black text-gray-600 hover:bg-gray-100 rounded-xl transition-colors uppercase tracking-widest">
                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Edit Profile
                        </a>

                        @if ($currentStaff)
                            <button wire:click="openModal"
                                class="w-full flex items-center px-4 py-2 text-[10px] font-black text-red-600 hover:bg-red-50 rounded-xl transition-colors uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Clock Out Staff
                            </button>
                        @else
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-2 text-[10px] font-black text-red-600 hover:bg-red-50 rounded-xl transition-colors uppercase tracking-widest">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                            stroke-width="2" />
                                    </svg>
                                    Logout Admin
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </aside>

    {{-- MODAL CLOCK OUT  --}}
    @if ($showClockOutModal)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fadeIn">
            <div class="bg-white w-full max-w-sm rounded-[3rem] p-8 text-center shadow-2xl animate-slideUp">
                <h3 class="text-xl font-black text-gray-900 uppercase italic">Konfirmasi Pulang</h3>
                <p class="text-[10px] text-gray-400 font-bold uppercase mt-1 mb-6 tracking-widest">Masukkan PIN Anda
                </p>

                <input type="password" wire:model="pin" maxlength="6" placeholder="••••••"
                    class="w-full text-center text-3xl tracking-[0.5em] font-black py-5 bg-gray-50 border-none rounded-3xl focus:ring-2 focus:ring-amber-900/20 mb-4">

                @error('pin')
                    <p class="text-xs text-red-600 font-bold mb-4 uppercase italic">{{ $message }}</p>
                @enderror

                <div class="flex gap-3">
                    <button wire:click="$set('showClockOutModal', false)"
                        class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batal</button>
                    <button wire:click="processClockOut"
                        class="flex-1 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gray-900/20 hover:bg-red-600 transition-all">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showClockOutModal)
        <div x-data="{ countdown: 3 }"
            x-on:start-clockout-redirect.window="let timer = setInterval(() => { countdown--; if(countdown <= 0) { clearInterval(timer); $wire.finalLogout() } }, 1000)"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fadeIn">

            <div
                class="bg-white w-full max-w-sm rounded-[3rem] p-8 text-center shadow-2xl animate-slideUp overflow-hidden relative">

                @if (!$isSuccess)
                    {{-- STEP 1: INPUT PIN --}}
                    <h3 class="text-xl font-black text-gray-900 uppercase italic">Konfirmasi Pulang</h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1 mb-6 tracking-widest">Masukkan PIN
                        Anda</p>

                    <input type="password" wire:model="pin" maxlength="6" placeholder="••••••"
                        class="w-full text-center text-3xl tracking-[0.5em] font-black py-5 bg-gray-50 border-none rounded-3xl focus:ring-2 focus:ring-amber-900/20 mb-4">

                    @error('pin')
                        <p class="text-xs text-red-600 font-bold mb-4 uppercase italic">{{ $message }}</p>
                    @enderror

                    <div class="flex gap-3">
                        <button wire:click="$set('showClockOutModal', false)"
                            class="flex-1 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Batal</button>
                        <button wire:click="processClockOut"
                            class="flex-1 py-4 bg-gray-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gray-900/20 hover:bg-red-600 transition-all">
                            Selesai
                        </button>
                    </div>
                @else
                    {{-- STEP 2: SUKSES --}}
                    <div class="py-4 animate-fadeIn">
                        <div
                            class="mb-6 inline-flex p-4 bg-green-500 text-white rounded-2xl shadow-lg shadow-green-500/30">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-black text-gray-900 uppercase italic">Sampai Jumpa!</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-2 mb-6 tracking-widest">
                            Clock-out Berhasil Dicatat
                        </p>

                        <div
                            class="text-[10px] font-black text-amber-900 bg-amber-50 py-2 px-4 rounded-full inline-block">
                            Redirecting in <span x-text="countdown"></span>s...
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
