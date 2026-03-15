<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeLevel extends Model
{
    protected $fillable = ['name', 'code', 'level', 'description'];

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
