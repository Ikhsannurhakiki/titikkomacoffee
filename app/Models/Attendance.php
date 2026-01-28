<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'approved_by',
        'clock_in',
        'clock_out',
        'duration_minutes',
        'status'
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    /**
     * Relasi: Absensi ini milik staf siapa?
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Relasi: Siapa user (Kepala/Admin) yang menyetujui absen ini?
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * Fungsi pembantu untuk menghitung durasi kerja dalam jam dan menit.
     */
    public function getWorkDurationAttribute()
    {
        if (!$this->clock_out) return 'Masih Bertugas';

        return $this->clock_in->diff($this->clock_out)->format('%H Jam %I Menit');
    }
}
