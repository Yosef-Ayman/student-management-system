<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Mathematics',        'code' => 'MATH',    'credit_hours' => 4],
            ['name' => 'Arabic Language',    'code' => 'ARAB',    'credit_hours' => 4],
            ['name' => 'English Language',   'code' => 'ENG',     'credit_hours' => 3],
            ['name' => 'Science',            'code' => 'SCI',     'credit_hours' => 4],
            ['name' => 'Physics',            'code' => 'PHYS',    'credit_hours' => 3],
            ['name' => 'Chemistry',          'code' => 'CHEM',    'credit_hours' => 3],
            ['name' => 'Biology',            'code' => 'BIO',     'credit_hours' => 3],
            ['name' => 'History',            'code' => 'HIST',    'credit_hours' => 2],
            ['name' => 'Geography',          'code' => 'GEO',     'credit_hours' => 2],
            ['name' => 'Islamic Studies',    'code' => 'ISL',     'credit_hours' => 2],
            ['name' => 'Computer Science',   'code' => 'CS',      'credit_hours' => 3],
            ['name' => 'Physical Education', 'code' => 'PE',      'credit_hours' => 1],
            ['name' => 'Art',                'code' => 'ART',     'credit_hours' => 1],
            ['name' => 'Social Studies',     'code' => 'SOC',     'credit_hours' => 2],
        ];

        foreach ($subjects as $subject) {
            Subject::create(array_merge($subject, [
                'description' => null,
                'is_active'   => true,
            ]));
        }
    }
}
