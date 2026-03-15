<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => 'Grade 1',  'code' => 'G1',  'level' => 1],
            ['name' => 'Grade 2',  'code' => 'G2',  'level' => 2],
            ['name' => 'Grade 3',  'code' => 'G3',  'level' => 3],
            ['name' => 'Grade 4',  'code' => 'G4',  'level' => 4],
            ['name' => 'Grade 5',  'code' => 'G5',  'level' => 5],
            ['name' => 'Grade 6',  'code' => 'G6',  'level' => 6],
            ['name' => 'Grade 7',  'code' => 'G7',  'level' => 7],
            ['name' => 'Grade 8',  'code' => 'G8',  'level' => 8],
            ['name' => 'Grade 9',  'code' => 'G9',  'level' => 9],
            ['name' => 'Grade 10', 'code' => 'G10', 'level' => 10],
            ['name' => 'Grade 11', 'code' => 'G11', 'level' => 11],
            ['name' => 'Grade 12', 'code' => 'G12', 'level' => 12],
        ];

        foreach ($levels as $level) {
            GradeLevel::create(array_merge($level, ['description' => null]));
        }
    }
}
