<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $year = AcademicYear::current();

        // ── KPI counts ───────────────────────────────────────────
        $stats = [
            'total_students' => User::query()->where('role', 'student')->where('is_active', true)->count(),
            'total_teachers' => User::query()->where('role', 'teacher')->where('is_active', true)->count(),
            'total_parents'  => User::query()->where('role', 'parent')->where('is_active', true)->count(),
            'total_classes'  => Classroom::where('is_active', true)->count(),
        ];

        // ── Attendance rate this week ─────────────────────────────
        $weekStart      = now()->startOfWeek();
        $totalRecords   = AttendanceRecord::where('created_at', '>=', $weekStart)->count();
        $presentRecords = AttendanceRecord::where('created_at', '>=', $weekStart)
                            ->whereIn('status', ['present', 'late'])->count();
        $stats['attendance_rate'] = $totalRecords > 0
            ? round(($presentRecords / $totalRecords) * 100, 1) : 0;

        $gradeDistribution = DB::table(DB::raw("(
            SELECT
                CASE
                    WHEN (marks_obtained / total_marks * 100) >= 95 THEN 'Excellent'
                    WHEN (marks_obtained / total_marks * 100) >= 90 THEN 'Very Good'
                    WHEN (marks_obtained / total_marks * 100) >= 80 THEN 'Good'
                    WHEN (marks_obtained / total_marks * 100) >= 70 THEN 'Average Fair'
                    WHEN (marks_obtained / total_marks * 100) >= 60 THEN 'Pass'
                    ELSE 'Failure'
                END as grade_role
            FROM grades
        ) as graded"))
        ->select('grade_role', DB::raw('COUNT(*) as count'))
        ->groupBy('grade_role')
        ->orderByRaw("FIELD(grade_role,'Excellent','Very Good','Good','Average Fair','Pass','Failure')")
        ->pluck('count', 'grade_role');

        $monthlyAvg = DB::table('grades')
            ->select(
                DB::raw('YEAR(graded_at) as year'),
                DB::raw('MONTH(graded_at) as month'),
                DB::raw('ROUND(AVG(marks_obtained / total_marks * 100), 1) as avg_pct')
            )
            ->where('graded_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('YEAR(graded_at)'), DB::raw('MONTH(graded_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // ── At-risk students (avg < 60%) ─────────────────────────
        $atRiskStudents = DB::table('grades')
            ->select(
                'student_id',
                DB::raw('ROUND(AVG(marks_obtained / total_marks * 100), 1) as avg_pct'),
                DB::raw('COUNT(*) as exam_count')
            )
            ->groupBy('student_id')
            ->having('avg_pct', '<', 60)
            ->orderBy('avg_pct')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $row->student = User::find($row->student_id);
                return $row;
            });

        // ── Attendance by class ───────────────────────────────────
        $classAttendance = Classroom::where('is_active', true)
            ->withCount(['enrollments as student_count' => fn($q) => $q->where('status', 'active')])
            ->with('gradeLevel')
            ->limit(8)
            ->get();

        // ── Recent students ───────────────────────────────────────
        $recentStudents = User::query()
            ->where('role', 'student')
            ->with(['studentProfile.classroom'])
            ->latest()
            ->limit(8)
            ->get();

        // ── Announcements ─────────────────────────────────────────
        $announcements = Announcement::where('is_published', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->with('author')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'gradeDistribution', 'monthlyAvg',
            'atRiskStudents', 'classAttendance',
            'recentStudents', 'announcements', 'year'
        ));
    }
}
