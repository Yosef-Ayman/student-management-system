<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $year    = AcademicYear::current();

        // ── Teacher's class-subjects this year ────────────────────
        $classSubjects = ClassSubject::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $year?->id)
            ->with(['classroom.gradeLevel', 'subject'])
            ->get();

        $classSubjectIds = $classSubjects->pluck('id');

        // ── KPIs ─────────────────────────────────────────────────
        $totalStudents = DB::table('enrollments')
            ->whereIn('classroom_id', $classSubjects->pluck('classroom_id'))
            ->where('status', 'active')
            ->distinct('student_id')
            ->count('student_id');

        $avgGrade = Grade::whereIn('class_subject_id', $classSubjectIds)
            ->avg(DB::raw('marks_obtained / total_marks * 100'));

        $weekStart      = now()->startOfWeek();
        $totalRec       = AttendanceRecord::whereHas('session', fn($q) =>
                                $q->whereIn('class_subject_id', $classSubjectIds)
                                  ->where('session_date', '>=', $weekStart))
                          ->count();
        $presentRec     = AttendanceRecord::whereHas('session', fn($q) =>
                                $q->whereIn('class_subject_id', $classSubjectIds)
                                  ->where('session_date', '>=', $weekStart))
                          ->whereIn('status', ['present','late'])
                          ->count();
        $attendanceRate = $totalRec > 0 ? round(($presentRec / $totalRec) * 100, 1) : 0;

        $atRiskCount = Grade::whereIn('class_subject_id', $classSubjectIds)
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('AVG(marks_obtained/total_marks*100) < 60')
            ->count();

        // ── Grade distribution per class-subject ─────────────────
        $gradeDistribution = Grade::whereIn('class_subject_id', $classSubjectIds)
            ->select(
                DB::raw("
                    CASE
                        WHEN (marks_obtained/total_marks*100) >= 90 THEN 'Very Good'
                        WHEN (marks_obtained/total_marks*100) >= 80 THEN 'Good'
                        WHEN (marks_obtained/total_marks*100) >= 70 THEN 'Average Fair'
                        WHEN (marks_obtained/total_marks*100) >= 60 THEN 'Pass'
                        ELSE 'Failure'
                    END as letter"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('letter')
            ->pluck('total','letter');

        // ── Upcoming exams ────────────────────────────────────────
        $upcomingExams = Exam::whereIn('class_subject_id', $classSubjectIds)
            ->where('exam_date', '>=', now()->toDateString())
            ->with(['classSubject.subject', 'classSubject.classroom'])
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // ── At-risk students ─────────────────────────────────────
        $atRiskStudents = Grade::whereIn('class_subject_id', $classSubjectIds)
            ->select('student_id',
                DB::raw('ROUND(AVG(marks_obtained/total_marks*100),1) as avg_pct'),
                DB::raw('COUNT(*) as exams_taken'))
            ->groupBy('student_id')
            ->having('avg_pct','<', 65)
            ->with('student:id,name,email')
            ->orderBy('avg_pct')
            ->limit(8)
            ->get();

        // ── Recent grades entered ─────────────────────────────────
        $recentGrades = Grade::where('graded_by', $teacher->id)
            ->with(['student:id,name', 'exam.classSubject.subject'])
            ->latest('graded_at')
            ->limit(6)
            ->get();

        // ── Unread messages ───────────────────────────────────────
        $unreadMessages = Message::where('receiver_id', $teacher->id)
            ->unread()
            ->with('sender:id,name,role')
            ->latest()
            ->limit(5)
            ->get();

        // ── Announcements ─────────────────────────────────────────
        $announcements = Announcement::published()
            ->forAudience('teachers')
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('teacher.dashboard', compact(
            'teacher', 'classSubjects', 'totalStudents', 'avgGrade',
            'attendanceRate', 'atRiskCount', 'gradeDistribution',
            'upcomingExams', 'atRiskStudents', 'recentGrades',
            'unreadMessages', 'announcements'
        ));
    }
}
