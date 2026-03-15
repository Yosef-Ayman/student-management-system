<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $classSubjects = ClassSubject::with('teacher')->get();
        $start = Carbon::now()->subWeeks(8);

        foreach ($classSubjects as $cs) {
            $date = $start->copy();
            for ($week = 0; $week < 8; $week++) {
                for ($day = 0; $day < 3; $day++) {
                    $sessionDate = $date->copy()->addDays($day * 2);

                    $session = AttendanceSession::create([
                        'class_subject_id' => $cs->id,
                        'taken_by'         => $cs->teacher_id,
                        'session_date'     => $sessionDate->toDateString(),
                        'session_time'     => '08:00:00',
                        'topic'            => 'Lesson ' . ($week * 3 + $day + 1),
                    ]);

                    $enrollments = Enrollment::where('classroom_id', $cs->classroom_id)
                        ->where('status', 'active')
                        ->get();

                    foreach ($enrollments as $enrollment) {
                        $statusRoll = rand(1, 20);
                        $status     = match (true) {
                            $statusRoll <= 15 => 'present',
                            $statusRoll <= 17 => 'late',
                            $statusRoll <= 19 => 'absent',
                            default           => 'excused',
                        };

                        AttendanceRecord::create([
                            'session_id'       => $session->id,
                            'student_id'       => $enrollment->student_id,
                            'status'           => $status,
                            'minutes_late'     => $status === 'late' ? rand(5, 20) : 0,
                            'parent_notified'  => $status === 'absent',
                            'notified_at'      => $status === 'absent' ? now() : null,
                        ]);
                    }
                }
                $date->addWeek();
            }
        }
    }
}
