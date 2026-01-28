<?php

use Livewire\Volt\Component;
use App\Models\Staff;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

new class extends Component {
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

        if ($staff) {
            $now = now('Asia/Jakarta');

            $lastAttendance = Attendance::where('staff_id', $staff->id)
                ->where('clock_in', '>=', $now->copy()->startOfDay())
                ->latest()
                ->first();

            if (!$lastAttendance || !empty($lastAttendance->clock_out)) {
                Attendance::create([
                    'staff_id' => $staff->id,
                    'clock_in' => $now,
                    'status' => 'present',
                ]);
                $this->lastStatus = 'MASUK';
            } else {
                $clockIn = \Illuminate\Support\Carbon::parse($lastAttendance->clock_in);
                $duration = (int) abs($clockIn->diffInMinutes($now)) * 60;

                $lastAttendance->update([
                    'clock_out' => $now,
                    'duration_minutes' => $duration,
                    'status' => 'completed',
                ]);
                $this->lastStatus = 'PULANG';
            }

            $this->lastStaff = $staff;
            $this->showSuccess = true;
            $this->reset(['pin', 'name']);
            $this->dispatch('auto-reset');
        } else {
            $this->addError('auth_failed', 'Kombinasi Nama atau PIN tidak ditemukan.');
        }
    }

    public function resetForm()
    {
        $this->showSuccess = false;
        $this->lastStaff = null;
        $this->resetErrorBag();
    }
}; ?>

<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-6"
    wire:poll.1s="$set('currentTime', '{{ now('Asia/Jakarta')->format('H:i:s') }}')" x-data="{ countdown: 0 }"
    x-on:auto-reset.window="countdown = 5; let timer = setInterval(() => { countdown--; if(countdown <= 0) { clearInterval(timer); $wire.resetForm() } }, 1000)">

    {{-- Header Waktu --}}
    <div class="text-center mb-8">
        <p class="text-sm text-secondary font-bold uppercase tracking-[0.2em]">
            {{ now()->isoFormat('dddd, D MMMM YYYY') }}
        </p>
        <h1 class="text-6xl font-black text-primary tracking-tighter">
            {{ $currentTime }}
        </h1>
    </div>

    <div class="w-full max-w-md relative transition-all duration-500">
        @if (!$showSuccess)
            {{-- FORM INPUT CARD --}}
            <div
                class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8 relative overflow-hidden uppercase">
                <div class="absolute top-0 left-0 w-full h-2 bg-secondary"></div>

                <div class="text-center mb-8">
                    <img src="{{ asset('images/logo-text-v2.png') }}" class="w-32 mx-auto mb-2 object-contain"
                        alt="Logo">
                    <h2 class="text-sm font-black text-primary tracking-widest">Attendance System</h2>
                </div>

                <div class="space-y-5">
                    {{-- Input Nama --}}
                    <div>
                        <input type="text" wire:model.live="name" placeholder="NAMA STAFF"
                            class="w-full text-center text-xl font-black py-3 bg-gray-50 border-2 border-dashed {{ $errors->has('name') || $errors->has('auth_failed') ? 'border-red-300' : 'border-gray-200' }} rounded-2xl focus:border-secondary focus:ring-0 transition-all text-primary">
                        @error('name')
                            <p class="text-2xs text-red-500 font-bold mt-1 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input PIN --}}
                    <div>
                        <input type="password" wire:model.live="pin" maxlength="6" placeholder="••••••"
                            class="w-full text-center text-3xl tracking-[0.5em] font-black py-3 bg-gray-50 border-2 border-dashed {{ $errors->has('pin') || $errors->has('auth_failed') ? 'border-red-300' : 'border-gray-200' }} rounded-2xl focus:border-secondary focus:ring-0 transition-all text-primary">

                        @if ($errors->has('auth_failed'))
                            <div class="mt-3 p-2 bg-red-50 border border-red-100 rounded-xl">
                                <p class="text-2xs text-red-500 font-bold text-center italic leading-tight">
                                    {{ $errors->first('auth_failed') }}
                                </p>
                            </div>
                        @endif
                        @error('pin')
                            <p class="text-2xs text-red-500 font-bold mt-1 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <button type="button" wire:click="$set('pin', ''); $set('name', ''); resetErrorBag()"
                            class="py-4 font-bold text-gray-400 bg-gray-50 rounded-2xl uppercase text-xs hover:bg-gray-100 transition">
                            Reset
                        </button>

                        <button type="button" wire:click="submitAbsen" wire:loading.attr="disabled"
                            class="py-4 bg-primary text-white rounded-2xl shadow-lg shadow-primary/20 flex justify-center items-center active:scale-95 transition disabled:opacity-50">
                            <svg wire:loading.remove wire:target="submitAbsen" class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span wire:loading wire:target="submitAbsen"
                                class="text-xs font-black animate-pulse">Checking...</span>
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- SUCCESS STATE CARD --}}
            <div
                class="bg-white rounded-[2.5rem] shadow-2xl border-2 border-secondary p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-secondary/10 rounded-full animate-pulse"></div>

                <div class="mb-6 inline-flex p-5 bg-green-50 text-green-500 rounded-full shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h2 class="text-4xl font-black text-primary mb-1 uppercase italic tracking-tighter">Berhasil
                    {{ $lastStatus }}!</h2>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-2xs mb-8">Data absensi telah
                    diperbarui</p>

                <div class="bg-gray-50 rounded-4xl p-6 mb-8 border border-gray-100 shadow-sm">
                    <p class="text-2xs font-black text-secondary uppercase tracking-widest mb-1">Staff Profile</p>
                    <h3 class="text-2xl font-black text-primary uppercase">{{ $lastStaff->name }}</h3>
                    <div
                        class="mt-4 pt-4 border-t border-gray-200/50 flex justify-center gap-6 text-2xs font-bold text-gray-400 uppercase tracking-widest">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                            </svg>
                            {{ now('Asia/Jakarta')->format('H:i') }} WIB
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-secondary" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd"
                                    d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                    clip-rule="evenodd" />
                            </svg>
                            RECORDED
                        </span>
                    </div>
                </div>

                <button wire:click="resetForm"
                    class="group text-2xs font-black text-gray-300 uppercase tracking-widest transition-all">
                    Selesai (<span x-text="countdown" class="text-secondary"></span>s)
                    <span class="block h-0.5 bg-gray-100 group-hover:bg-secondary transition-all mt-1"></span>
                </button>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <p class="mt-12 text-2xs font-bold text-gray-300 uppercase tracking-[0.3em]">
        Titik Koma Coffee &bull; Secure Attendance Gate
    </p>
</div>
