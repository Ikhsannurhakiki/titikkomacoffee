<?php

namespace App\View\Components;

use Closure;
use App\Models\Staff;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class sidebar extends Component
{
    /**
     * Create a new component instance.
     */

    public $currentStaff;

    public function __construct()
    {
        $staffId = session('current_staff.id');
        $this->currentStaff = $staffId ? Staff::find($staffId) : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar');
    }

    public function processClockOut($inputPin)
    {
        $staffData = session('current_staff');

        // 1. Ambil data staff asli dari DB untuk verifikasi PIN
        $staff = \App\Models\Staff::find($staffData['id']);

        if ($staff && $staff->pin === $inputPin) {
            $now = now('Asia/Jakarta');

            // 2. Cari absensi yang masih 'Open'
            $attendance = \App\Models\Attendance::where('staff_id', $staff->id)
                ->whereNull('clock_out')
                ->latest()
                ->first();

            if ($attendance) {
                $clockIn = \Illuminate\Support\Carbon::parse($attendance->clock_in);
                $duration = (int) $clockIn->diffInMinutes($now);

                $attendance->update([
                    'clock_out' => $now,
                    'duration_minutes' => $duration,
                    'status' => 'completed',
                ]);
            }

            // 3. Hapus Session & Redirect
            session()->forget('current_staff');
            session()->save();

            return redirect()->route('role-login');
        }

        // Jika PIN salah
        $this->dispatch('pin-error');
    }
}
