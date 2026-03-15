<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        $year     = AcademicYear::where('is_current', true)->first();
        $teachers = User::teachers()->get();
        $sections = ['A', 'B', 'C'];

        foreach ([8, 9, 10, 11, 12] as $levelNum) {
            $level = GradeLevel::where('level', $levelNum)->first();
            foreach ($sections as $si => $section) {
                Classroom::create([
                    'name'               => "Grade {$levelNum}-{$section}",
                    'grade_level_id'     => $level->id,
                    'academic_year_id'   => $year->id,
                    'homeroom_teacher_id'=> $teachers->get(($si) % $teachers->count())?->id,
                    'capacity'           => 30,
                    'room_number'        => ($levelNum * 10) + ($si + 1),
                    'is_active'          => true,
                ]);
            }
        }
    }
}
