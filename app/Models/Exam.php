<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'title', 'class_subject_id', 'academic_year_id', 'type',
        'total_marks', 'pass_marks', 'weight_percentage',
        'exam_date', 'starts_at', 'ends_at',
        'instructions', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'exam_date'      => 'date',
            'starts_at'      => 'datetime',
            'ends_at'        => 'datetime',
            'total_marks'    => 'decimal:2',
            'pass_marks'     => 'decimal:2',
            'weight_percentage' => 'decimal:2',
            'is_published'   => 'boolean',
        ];
    }

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getAverageScoreAttribute(): float
    {
        return $this->grades()->avg('marks_obtained') ?? 0;
    }

    public function getPassRateAttribute(): float
    {
        $total = $this->grades()->count();
        if ($total === 0) return 0;
        $passed = $this->grades()
            ->whereRaw('marks_obtained >= ?', [$this->pass_marks])
            ->count();
        return round(($passed / $total) * 100, 1);
    }
}
