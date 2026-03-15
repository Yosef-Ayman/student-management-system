<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::admins()->first();

        foreach (Enrollment::with(['classroom.classSubjects.exams'])->get() as $enrollment) {
            foreach ($enrollment->classroom->classSubjects as $cs) {
                foreach ($cs->exams as $exam) {
                    $marksObtained = $this->randomMark($exam->total_marks);

                    Grade::create([
                        'student_id'      => $enrollment->student_id,
                        'exam_id'         => $exam->id,
                        'class_subject_id'=> $cs->id,
                        'graded_by'       => $cs->teacher_id ?? $admin->id,
                        'marks_obtained'  => $marksObtained,
                        'total_marks'     => $exam->total_marks,
                        'is_absent'       => false,
                        'graded_at'       => now()->subDays(rand(1, 30)),
                    ]);
                }
            }
        }
    }

    private function randomMark(float $total): float
    {
        $pct = match (rand(1, 10)) {
            1       => rand(30, 59),
            2, 3    => rand(60, 69),
            4, 5, 6 => rand(70, 84),
            default => rand(85, 100),
        };
        return round(($pct / 100) * $total, 2);
    }
}
