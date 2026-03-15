<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'session_id', 'student_id', 'status',
        'minutes_late', 'excuse_reason',
        'parent_notified', 'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'parent_notified' => 'boolean',
            'notified_at'     => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'present' => 'success',
            'absent'  => 'danger',
            'late'    => 'warning',
            'excused' => 'secondary',
            default   => 'light',
        };
    }

    public function scopeAbsent($query)  { return $query->where('status', 'absent'); }
    public function scopePresent($query) { return $query->where('status', 'present'); }
    public function scopeLate($query)    { return $query->where('status', 'late'); }
}
