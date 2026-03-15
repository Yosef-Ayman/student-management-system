<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $year    = AcademicYear::current();

        // ── Current enrollment ────────────────────────────────────
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('academic_year_id', $year?->id)
            ->where('status','active')
            ->with(['classroom.gradeLevel','classroom.homeroomTeacher'])
            ->first();

        $classroom = $enrollment?->classroom;

        // ── Class subjects (schedule) ────────────────────────────
        $classSubjects = $classroom
            ? ClassSubject::where('classroom_id', $classroom->id)
                ->where('academic_year_id', $year?->id)
                ->with(['subject','teacher'])
                ->get()
            : collect();

        // ── Grades per subject ────────────────────────────────────
        $gradesBySubject = Grade::where('student_id', $student->id)
            ->whereIn('class_subject_id', $classSubjects->pluck('id'))
            ->with(['classSubject.subject','exam'])
            ->get()
            ->groupBy('class_subject_id');

        $subjectAverages = $classSubjects->map(function($cs) use ($gradesBySubject) {
            $grades  = $gradesBySubject->get($cs->id, collect());
            $avg     = $grades->count() > 0
                ? round($grades->avg(fn($g) => $g->marks_obtained / $g->total_marks * 100), 1)
                : null;
            return [
                'class_subject' => $cs,
                'subject'       => $cs->subject,
                'teacher'       => $cs->teacher,
                'average'       => $avg,
                'letter'        => $this->letterFromPct($avg),
                'grades'        => $grades,
            ];
        });

        // ── Overall average ───────────────────────────────────────
        $overallAvg = Grade::where('student_id', $student->id)
            ->avg(DB::raw('marks_obtained/total_marks*100'));

        // ── Class rank ────────────────────────────────────────────
        $rank = null;
        if ($classroom && $overallAvg !== null) {
            $rank = DB::table('grades as g')
                ->join('enrollments as e', 'e.student_id', '=', 'g.student_id')
                ->where('e.classroom_id', $classroom->id)
                ->where('e.status','active')
                ->select('g.student_id')
                ->groupBy('g.student_id')
                ->havingRaw('AVG(g.marks_obtained/g.total_marks*100) > ?', [$overallAvg])
                ->get()
                ->count() + 1;
        }

        // ── Attendance summary ────────────────────────────────────
        $attendance = AttendanceRecord::where('student_id', $student->id)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(status="present") as present'),
                DB::raw('SUM(status="absent") as absent'),
                DB::raw('SUM(status="late") as late'),
                DB::raw('ROUND(SUM(status IN ("present","late"))/COUNT(*)*100,1) as rate')
            )
            ->first();

        // ── Upcoming exams ────────────────────────────────────────
        $upcomingExams = Exam::whereIn('class_subject_id', $classSubjects->pluck('id'))
            ->where('exam_date', '>=', now()->toDateString())
            ->where('is_published', true)
            ->with(['classSubject.subject'])
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // ── Recent grades ─────────────────────────────────────────
        $recentGrades = Grade::where('student_id', $student->id)
            ->with(['exam','classSubject.subject'])
            ->latest('graded_at')
            ->limit(6)
            ->get();

        // ── Announcements ─────────────────────────────────────────
        $announcements = Announcement::published()
            ->forAudience('students')
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('student.dashboard', compact(
            'student', 'enrollment', 'classroom', 'classSubjects',
            'subjectAverages', 'overallAvg', 'rank', 'attendance',
            'upcomingExams', 'recentGrades', 'announcements'
        ));
    }

    private function letterFromPct(?float $pct): string
    {
        if ($pct === null) return '—';
        return match(true) {
            $pct >= 95 => 'Excellent', $pct >= 90 => 'Very Good',
            $pct >= 80 => 'Good', $pct >= 70 => 'Average Fair',
            $pct >= 60 => 'Pass', default => 'Failure',
        };
    }
}
