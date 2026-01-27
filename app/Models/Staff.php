<?php

namespace App\Models;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Staff extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'staffs';

    protected $fillable = [
        'name',
        'pin',
        'position',
        'is_active',
        'phone',
        'join_date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('staff-profile')
            ->singleFile();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
