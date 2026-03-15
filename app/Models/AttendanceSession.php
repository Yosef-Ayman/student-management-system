<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    protected $fillable = [
        'class_subject_id', 'taken_by',
        'session_date', 'session_time', 'topic', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function takenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'session_id');
    }

    public function getPresentCountAttribute(): int
    {
        return $this->records()->where('status', 'present')->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->records()->where('status', 'absent')->count();
    }

    public function getLateCountAttribute(): int
    {
        return $this->records()->where('status', 'late')->count();
    }

    public function getAttendanceRateAttribute(): float
    {
        $total = $this->records()->count();
        if ($total === 0) return 0;
        $present = $this->records()->whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 1);
    }
}
