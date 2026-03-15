<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $year      = AcademicYear::where('is_current', true)->first();
        $teachers  = User::teachers()->get();
        $subjects  = Subject::active()->get();
        $classrooms= Classroom::active()->get();

        $schedules = [
            'Sun,Tue,Thu 08:00-09:00',
            'Sun,Tue,Thu 09:00-10:00',
            'Mon,Wed     08:00-09:00',
            'Mon,Wed     09:00-10:00',
            'Sun,Tue     10:30-11:30',
        ];

        $coreSubjectCodes = ['MATH', 'ARAB', 'ENG', 'SCI', 'HIST'];
        $coreSubjects     = $subjects->whereIn('code', $coreSubjectCodes)->values();

        foreach ($classrooms as $ci => $classroom) {
            foreach ($coreSubjects as $si => $subject) {
                ClassSubject::create([
                    'classroom_id'    => $classroom->id,
                    'subject_id'      => $subject->id,
                    'teacher_id'      => $teachers->get(($ci + $si) % $teachers->count())->id,
                    'academic_year_id'=> $year->id,
                    'schedule'        => $schedules[$si % count($schedules)],
                    'room_number'     => $classroom->room_number,
                    'is_active'       => true,
                ]);
            }
        }
    }
}
