<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $year        = AcademicYear::where('is_current', true)->first();
        $classSubjects = ClassSubject::all();

        $examTypes = [
            ['type' => 'quiz',    'title' => 'Quiz 1',     'total' => 20,  'pass' => 10,  'weight' => 10],
            ['type' => 'quiz',    'title' => 'Quiz 2',     'total' => 20,  'pass' => 10,  'weight' => 10],
            ['type' => 'midterm', 'title' => 'Midterm',    'total' => 50,  'pass' => 25,  'weight' => 30],
            ['type' => 'final',   'title' => 'Final Exam', 'total' => 100, 'pass' => 50,  'weight' => 50],
        ];

        foreach ($classSubjects as $cs) {
            foreach ($examTypes as $i => $et) {
                Exam::create([
                    'title'              => $et['title'] . ' — ' . $cs->subject->name,
                    'class_subject_id'   => $cs->id,
                    'academic_year_id'   => $year->id,
                    'type'               => $et['type'],
                    'total_marks'        => $et['total'],
                    'pass_marks'         => $et['pass'],
                    'weight_percentage'  => $et['weight'],
                    'exam_date'          => now()->addWeeks($i * 4)->toDateString(),
                    'is_published'       => true,
                ]);
            }
        }
    }
}
