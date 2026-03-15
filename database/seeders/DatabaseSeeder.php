<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AcademicYearSeeder::class,
            GradeLevelSeeder::class,
            SubjectSeeder::class,
            UserSeeder::class,
            ClassroomSeeder::class,
            ClassSubjectSeeder::class,
            EnrollmentSeeder::class,
            ExamSeeder::class,
            GradeSeeder::class,
            AttendanceSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
