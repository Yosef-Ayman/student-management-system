<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $year      = AcademicYear::where('is_current', true)->first();
        $students  = User::students()->get();
        $classrooms= Classroom::active()->get();

        foreach ($students as $i => $student) {
            $classroom = $classrooms->get($i % $classrooms->count());

            Enrollment::create([
                'student_id'       => $student->id,
                'classroom_id'     => $classroom->id,
                'academic_year_id' => $year->id,
                'enrolled_at'      => $year->start_date,
                'status'           => 'active',
            ]);

            $student->studentProfile?->update(['classroom_id' => $classroom->id]);
        }
    }
}
