<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherProfile extends Model
{
    protected $fillable = [
        'user_id', 'employee_code', 'qualification',
        'specialization', 'hire_date', 'salary',
        'national_id', 'bio',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary'    => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
