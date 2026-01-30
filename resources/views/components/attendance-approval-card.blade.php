@props(['attendance'])

<div
    {{ $attributes->merge(['class' => 'flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 group transition-all']) }}>
    {{-- Bagian Kiri: Info Staf --}}
    <div class="flex items-center gap-3">
        <div
            class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm">
            {{ strtoupper(substr($attendance->staff->name ?? '??', 0, 2)) }}
        </div>
        <div>
            <h4 class="text-sm font-bold text-gray-800">{{ $attendance->staff->name ?? 'Unknown' }}</h4>
            <div class="flex items-center gap-2">
                <span
                    class="text-2xs px-1.5 py-0.5 rounded bg-gray-200 text-gray-600 font-bold uppercase tracking-tighter">
                    {{ $attendance->staff->position ?? 'Staff' }}
                </span>
                <span class="text-2xs text-gray-400 font-medium">
                    In: {{ $attendance->clock_in?->format('H:i') ?? '--:--' }}
                </span>
            </div>
        </div>
    </div>

    <div class="text-right">
        @if ($attendance->approved_by)
            <p class="text-[9px] text-gray-400 font-bold uppercase">Verifier:</p>
            <p class="text-[11px] font-bold text-gray-600">{{ $attendance->approver->name ?? 'System' }}</p>
            <button wire:click="cancelApprove({{ $attendance->id }})"
                class="text-[9px] font-bold text-red-400 hover:text-red-600 transition-colors uppercase mt-1">
                Cancel
            </button>
        @else
            <button wire:click="approve({{ $attendance->id }})" style="background-color: #b58d69;"
                class="text-white text-2xs font-bold px-4 py-2 rounded-lg shadow-sm transition-all active:scale-95 hover:brightness-90">
                APPROVE
            </button>
        @endif
    </div>
</div>
