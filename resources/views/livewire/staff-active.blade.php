<?php

use Livewire\Volt\Component;
use App\Models\Attendance;
use Carbon\Carbon;

new class extends Component {
    public $showPinModal = false;
    public $pinInput = '';
    public $selectedAttendanceId = null;
    public $errorMessage = '';

    public function with()
    {
        $all = Attendance::with('staff', 'approver')->whereDate('clock_in', Carbon::today())->whereNull('clock_out')->latest()->get();

        return [
            'pendingAttendances' => $all->whereNull('approved_by'),
            'approvedAttendances' => $all->whereNotNull('approved_by'),
        ];
    }

    public function askForPin($attendanceId)
    {
        $this->selectedAttendanceId = $attendanceId;
        $this->pinInput = '';
        $this->errorMessage = '';
        $this->showPinModal = true;
    }

    public function verifyAndApprove()
    {
        $authorizedPin = '1234';

        if ($this->pinInput === $authorizedPin) {
            Attendance::find($this->selectedAttendanceId)?->update([
                'approved_by' => auth()->id(),
                'status' => 'approved',
            ]);

            $this->showPinModal = false;
            $this->reset(['pinInput', 'selectedAttendanceId', 'errorMessage']);
        } else {
            $this->errorMessage = 'PIN Salah! Akses ditolak.';
            $this->pinInput = '';
        }
    }

    public function cancelApprove($attendanceId)
    {
        Attendance::find($attendanceId)?->update([
            'approved_by' => null,
            'status' => 'pending',
        ]);
    }
}; ?>

<div class="relative min-h-screen bg-gray-50/50 p-6">
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- SECTION 1: WAITING APPROVAL --}}
        @if ($pendingAttendances->count() > 0)
            <div
                class="bg-white rounded-2xl shadow-sm border-2 border-amber-100 p-6 animate-in slide-in-from-top duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-black text-amber-800">Menunggu Persetujuan</h2>
                        <p class="text-2xs text-amber-500 font-bold uppercase tracking-widest">Verifikasi kehadiran
                            staf sekarang</p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-black ring-4 ring-amber-50">
                        {{ $pendingAttendances->count() }} REQUEST
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($pendingAttendances as $attendance)
                        <div
                            class="flex items-center justify-between p-3 rounded-xl bg-amber-50/30 border border-amber-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-amber-200 flex items-center justify-center text-amber-700 font-bold text-sm">
                                    {{ strtoupper(substr($attendance->staff->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800">{{ $attendance->staff->name }}</h4>
                                    <p class="text-2xs text-amber-600 font-medium italic">Menunggu verifikasi...</p>
                                </div>
                            </div>
                            <button wire:click="askForPin({{ $attendance->id }})" style="background-color: #b58d69;"
                                class="text-white text-2xs font-black px-5 py-2.5 rounded-xl shadow-md shadow-orange-200 active:scale-95 transition-all">
                                APPROVE
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SECTION 2: ACTIVE STAFF --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-black text-gray-800">Staf Aktif</h2>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Sedang Bertugas Hari Ini</p>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50 rounded-lg">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-bold text-green-700">{{ $approvedAttendances->count() }} Orang</span>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($approvedAttendances as $attendance)
                    <div
                        class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 border border-gray-100 group transition-all">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">
                                {{ strtoupper(substr($attendance->staff->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">{{ $attendance->staff->name }}</h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span
                                        class="text-[9px] px-1.5 py-0.5 rounded bg-gray-200 text-gray-500 font-black uppercase">{{ $attendance->staff->position }}</span>
                                    <span class="text-2xs text-gray-400">In:
                                        {{ $attendance->clock_in->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-[8px] text-gray-400 font-black uppercase tracking-tighter">Verifier</p>
                            <p class="text-[11px] font-bold text-gray-600">{{ $attendance->approver->name }}</p>
                            <button wire:click="cancelApprove({{ $attendance->id }})"
                                class="text-[9px] font-bold text-red-400 hover:text-red-600 transition-colors uppercase mt-1">
                                Cancel
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">Belum ada staf yang terverifikasi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL PIN OVERLAY --}}
    @if ($showPinModal)
        <div
            class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md transition-all">
            <div
                class="bg-white w-full max-w-xs rounded-3xl shadow-2xl p-8 text-center animate-in zoom-in-95 duration-200">
                <div
                    class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-orange-50/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" style="color: #b58d69;" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <h3 class="text-xl font-black text-gray-800 mb-1">Verifikasi PIN</h3>
                <p class="text-xs text-gray-400 mb-8 font-medium">Otoritas Cashier / Head Kitchen</p>

                <div class="relative mb-4">
                    <input type="password" wire:model="pinInput" wire:keydown.enter="verifyAndApprove" autofocus
                        maxlength="4" placeholder="••••"
                        class="w-full text-center text-3xl tracking-[0.8em] font-black border-none bg-gray-50 rounded-2xl py-4 focus:ring-2 focus:ring-[#b58d69] transition-all">
                </div>

                @if ($errorMessage)
                    <div class="bg-red-50 text-red-500 text-2xs font-bold py-2 px-4 rounded-lg mb-6">
                        {{ $errorMessage }}
                    </div>
                @endif

                <div class="flex flex-col gap-2">
                    <button wire:click="verifyAndApprove" style="background-color: #b58d69;"
                        class="w-full text-white font-black py-4 rounded-2xl shadow-lg shadow-orange-100 active:scale-95 transition-all">
                        VERIFIKASI SEKARANG
                    </button>
                    <button wire:click="$set('showPinModal', false)"
                        class="text-xs font-bold text-gray-400 py-3 hover:text-gray-600 transition-colors">
                        BATALKAN
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
