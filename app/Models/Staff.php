<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    // Nama tabel jika tidak jamak (optional, tapi baik untuk kejelasan)
    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'pin',
        'position',
        'is_active'
    ];

    /**
     * Relasi: Satu staf memiliki banyak catatan absensi.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope untuk mempermudah filter staf yang sedang aktif bekerja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
