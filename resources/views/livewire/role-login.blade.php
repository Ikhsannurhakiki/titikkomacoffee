<?php

use Livewire\Volt\Component;
use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {
    public $name = '';
    public $pin = '';
    public $currentTime;
    public $showSuccess = false;
    public $lastStaff = null;
    public $lastStatus = '';

    public function mount()
    {
        $this->currentTime = now('Asia/Jakarta')->format('H:i:s');
    }

    public function submitAbsen()
    {
        $this->validate([
            'name' => 'required|string',
            'pin' => 'required|digits:6',
        ]);

        $staff = Staff::where('name', $this->name)->where('pin', $this->pin)->first();

        if (!$staff) {
            $this->addError('auth_failed', 'Kombinasi Nama atau PIN tidak ditemukan.');
            return;
        }

        $now = now('Asia/Jakarta');

        $activeAttendance = Attendance::where('staff_id', $staff->id)->whereNull('clock_out')->latest()->first();

        session()->put('current_staff', [
            'id' => $staff->id,
            'name' => $staff->name,
            'position' => $staff->position ?? 'staff',
        ]);
        session()->save();
        if ($activeAttendance) {
            return redirect()->to('/dashboard');
        } else {
            Attendance::create([
                'staff_id' => $staff->id,
                'clock_in' => $now,
                'status' => 'present',
            ]);

            $this->lastStatus = 'CHECK-IN BERHASIL';
            $this->lastStaff = $staff;
            $this->showSuccess = true;
            return redirect()->to('/dashboard');
        }
    }

    public function resetForm()
    {
        $this->showSuccess = false;
        $this->lastStaff = null;
        $this->reset(['pin', 'name']);
        $this->resetErrorBag();
    }
}; ?>

<div class="min-h-screen bg-[#f8f9fa] flex flex-col items-center justify-center p-6 font-sans antialiased"
    wire:poll.1s="$set('currentTime', '{{ now('Asia/Jakarta')->format('H:i:s') }}')" x-data="{ countdown: 0 }"
    x-on:auto-reset.window="countdown = 5; let timer = setInterval(() => { countdown--; if(countdown <= 0) { clearInterval(timer); $wire.resetForm() } }, 1000)">

    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-900/5 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-amber-600/5 blur-[120px] rounded-full"></div>
    </div>

    <div class="text-center mb-10 relative z-10 animate-fadeIn">
        <div class="flex items-center justify-center space-x-3 mb-2">
            <span class="h-px w-8 bg-amber-900/20"></span>
            <p class="text-[11px] font-black text-amber-900/60 uppercase tracking-[0.4em]">
                {{ now()->isoFormat('dddd, D MMMM YYYY') }}
            </p>
            <span class="h-px w-8 bg-amber-900/20"></span>
        </div>
        <h1 class="text-6xl font-black text-gray-900 tracking-tighter">
            {{ $currentTime }}
        </h1>
    </div>

    <div class="w-full max-w-md relative">
        @if (!$showSuccess)
            <div
                class="bg-white rounded-[3.5rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.08)] border border-gray-100 overflow-hidden animate-slideUp">
                <div class="absolute top-0 left-0 w-full h-2 bg-amber-900"></div>

                <div class="p-6 md:p-6">
                    <div class="text-center mb-10">
                        <img src="{{ asset('images/logo-text-v2.png') }}" class="h-50 mx-auto object-contain"
                            alt="Logo">
                        <h2 class="text-md font-black text-gray-400 tracking-[0.3em] uppercase">Hello!
                        </h2>
                    </div>

                    <div class="space-y-6">
                        <div class="relative">
                            <input type="text" wire:model.live="name" placeholder="Name"
                                class="w-full text-center text-md font-black py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-amber-900/20 focus:ring-0 transition-all text-gray-900 placeholder:text-gray-300">
                            @error('name')
                                <p class="text-[10px] text-red-500 font-bold mt-2 text-center uppercase tracking-wide">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <input type="password" wire:model.live="pin" maxlength="6" placeholder="••••••"
                                class="w-full text-center text-md tracking-[0.5em] font-black py-4 bg-gray-50 border-2 border-transparent rounded-2xl focus:bg-white focus:border-amber-900/20 focus:ring-0 transition-all text-amber-900 placeholder:text-gray-300">
                            @error('pin')
                                <p class="text-[10px] text-red-500 font-bold mt-2 text-center uppercase tracking-wide">
                                    {{ $message }}</p>
                            @enderror
                        </div>

                        @if ($errors->has('auth_failed'))
                            <div class="p-4 bg-red-50 rounded-2xl border border-red-100 animate-pulse">
                                <p class="text-[11px] text-red-600 font-bold text-center italic uppercase">
                                    {{ $errors->first('auth_failed') }}</p>
                            </div>
                        @endif

                        <div class="flex gap-3 pt-2">
                            <button type="button" wire:click="resetForm"
                                class="w-1/3 py-4 font-black text-gray-400 bg-gray-50 rounded-2xl uppercase text-[10px] tracking-widest hover:bg-gray-100 transition shadow-sm border border-gray-100">
                                Clear
                            </button>

                            <button type="button" wire:click="submitAbsen" wire:loading.attr="disabled"
                                class="flex-1 py-4 bg-gray-900 text-white rounded-2xl shadow-xl shadow-gray-900/20 flex justify-center items-center active:scale-95 transition-all hover:bg-amber-900">
                                <span wire:loading.remove wire:target="submitAbsen"
                                    class="font-black text-[10px] uppercase tracking-widest">Verify ID</span>
                                <span wire:loading wire:target="submitAbsen"
                                    class="text-[10px] font-black animate-pulse uppercase">Checking...</span>
                            </button>
                        </div>
                        <div class=" border-t border-gray-100 text-center">
                            <p class="text-xs text-secondary mb-3">Bukan Staff? Masuk sebagai Pengelola</p>

                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-xl font-bold text-[10px] text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-1.116-13.59c1.973-1.85 4.59-3.033 7.469-3.033 4.418 0 8 3.582 8 8 0 2.828-1.465 5.313-3.692 6.746" />
                                </svg>
                                Admin Portal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div
                class="bg-white rounded-[3.5rem] shadow-[0_40px_80px_-15px_rgba(0,0,0,0.12)] border-2 border-amber-900/10 p-10 text-center relative overflow-hidden animate-fadeIn">
                <div class="absolute -top-12 -right-12 w-40 h-40 bg-amber-900/5 rounded-full animate-pulse"></div>

                <div class="mb-8 inline-flex p-6 bg-amber-900 text-white rounded-3xl shadow-2xl shadow-amber-900/30">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h2 class="text-4xl font-black text-gray-900 mb-2 uppercase tracking-tighter italic">
                    {{ $lastStatus }}!
                </h2>
                <p class="text-gray-400 font-bold uppercase tracking-[0.2em] text-[10px] mb-10">Koneksi Berhasil & Data
                    Tersimpan</p>

                <div class="bg-gray-50 rounded-[2rem] p-8 mb-10 border border-gray-100 relative group transition-all">
                    <p class="text-[9px] font-black text-amber-900/40 uppercase tracking-[0.4em] mb-2">Authenticated
                        Staff</p>
                    <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tight">{{ $lastStaff->name }}</h3>

                    <div
                        class="mt-6 pt-6 border-t border-gray-200/60 flex justify-center gap-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-900 rounded-full animate-ping"></span>
                            {{ now('Asia/Jakarta')->format('H:i') }} WIB
                        </span>
                        <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[8px]">LOGGED</span>
                    </div>
                </div>

                <button wire:click="resetForm" class="group inline-flex flex-col items-center">
                    <span
                        class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em] group-hover:text-amber-900 transition-colors">
                        Selesai (<span x-text="countdown" class="text-amber-900"></span>s)
                    </span>
                    <span
                        class="w-8 h-1 bg-gray-100 rounded-full mt-2 group-hover:w-16 group-hover:bg-amber-900 transition-all duration-500"></span>
                </button>
            </div>
        @endif
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .animate-slideUp {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</div>
