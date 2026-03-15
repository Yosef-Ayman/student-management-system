<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id', 'exam_id', 'class_subject_id',
        'graded_by', 'marks_obtained', 'total_marks',
        'is_absent', 'remarks', 'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
            'total_marks'    => 'decimal:2',
            'is_absent'      => 'boolean',
            'graded_at'      => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getPercentageAttribute(): float
    {
        if ($this->total_marks == 0) return 0;
        return round(($this->marks_obtained / $this->total_marks) * 100, 2);
    }

    public function getLetterGradeAttribute(): string
    {
        $pct = $this->percentage;
        return match (true) {
            $pct >= 95 => 'Excellent', $pct >= 90 => 'Very Good',
            $pct >= 80 => 'Good', $pct >= 70 => 'Average Fair',
            $pct >= 60 => 'Pass', default => 'Failure',
        };
    }

    public function getGradeBadgeColorAttribute(): string
    {
        return match ($this->grade_role) {
            'Excellent', 'Very Good'    => 'success',
            'Good', 'Average Fair'      => 'primary',
            'Pass'                      => 'warning',
            default                     => 'danger',
        };
    }

    public function scopePassed($query)
    {
        return $query->whereRaw('marks_obtained >= (SELECT pass_marks FROM exams WHERE id = grades.exam_id)');
    }

    public function scopeFailed($query)
    {
        return $query->whereRaw('marks_obtained < (SELECT pass_marks FROM exams WHERE id = grades.exam_id)');
    }
}
