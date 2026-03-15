<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $year = AcademicYear::current();

        // ── Overview KPIs ─────────────────────────────────────────
        $overallAvg = DB::table('grades')
            ->avg(DB::raw('marks_obtained / total_marks * 100'));

        $totalRecords   = DB::table('attendance_records')->count();
        $presentRecords = DB::table('attendance_records')
            ->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $totalRecords > 0
            ? round(($presentRecords / $totalRecords) * 100, 1) : 0;

        $atRiskCount = DB::table('grades')
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('AVG(marks_obtained / total_marks * 100) < 60')
            ->get()->count();

        $parentEngagement = DB::table('parent_student')
            ->where('receive_notifications', true)->count();

        // ── Grade distribution — subquery for strict mode ─────────
        $gradeDistribution = DB::table(DB::raw("(
            SELECT
                CASE
                    WHEN (marks_obtained / total_marks * 100) >= 95 THEN 'Excellent'
                    WHEN (marks_obtained / total_marks * 100) >= 90 THEN 'Very Good'
                    WHEN (marks_obtained / total_marks * 100) >= 80 THEN 'Good'
                    WHEN (marks_obtained / total_marks * 100) >= 70 THEN 'Average Fair'
                    WHEN (marks_obtained / total_marks * 100) >= 60 THEN 'Pass'
                    ELSE 'Failure'
                END as letter
            FROM grades
        ) as graded"))
        ->select('letter', DB::raw('COUNT(*) as total'))
        ->groupBy('letter')
        ->orderByRaw("FIELD(letter,'Excellent','Very Good','Good','Average Fair','Pass','Failure')")
        ->pluck('total', 'letter');

        $monthlyTrend = DB::table('grades')
            ->select(
                DB::raw('DATE_FORMAT(graded_at, "%b %Y") as month_label'),
                DB::raw('ROUND(AVG(marks_obtained/total_marks*100),1) as avg_pct')
            )
            ->where('graded_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('DATE_FORMAT(graded_at, "%b %Y")'), DB::raw('YEAR(graded_at)'), DB::raw('MONTH(graded_at)'))
            ->orderByRaw('YEAR(graded_at), MONTH(graded_at)')
            ->get()
            ->map(function ($row) {
                $row->att_pct = 0;
                return $row;
            });

        $classPerformance = DB::table('classrooms')
            ->select(
                'classrooms.id',
                'classrooms.name',
                DB::raw('ROUND(AVG(g.marks_obtained/g.total_marks*100),1) as avg_grade'),
                DB::raw('COUNT(DISTINCT e.student_id) as student_count')
            )
            ->leftJoin('enrollments as e', function ($j) {
                $j->on('e.classroom_id', '=', 'classrooms.id')
                  ->where('e.status', '=', 'active');
            })
            ->leftJoin('class_subjects as cs', 'cs.classroom_id', '=', 'classrooms.id')
            ->leftJoin('grades as g', function ($j) {
                $j->on('g.student_id', '=', 'e.student_id')
                  ->on('g.class_subject_id', '=', 'cs.id');
            })
            ->where('classrooms.is_active', true)
            ->groupBy('classrooms.id', 'classrooms.name')
            ->orderByDesc('avg_grade')
            ->get();

        // Attach gradeLevel name via a separate query to keep it simple
        $gradeLevelMap = DB::table('classrooms')
            ->join('grade_levels', 'grade_levels.id', '=', 'classrooms.grade_level_id')
            ->pluck('grade_levels.name', 'classrooms.id');

        $classPerformance = $classPerformance->map(function ($c) use ($gradeLevelMap) {
            $c->grade_level_name = $gradeLevelMap[$c->id] ?? '';
            return $c;
        });

        // ── Performance by subject ────────────────────────────────
        $subjectPerformance = DB::table('subjects')
            ->select(
                'subjects.id',
                'subjects.name',
                DB::raw('ROUND(AVG(g.marks_obtained/g.total_marks*100),1) as avg_grade'),
                DB::raw('COUNT(g.id) as grade_count')
            )
            ->leftJoin('class_subjects as cs', 'cs.subject_id', '=', 'subjects.id')
            ->leftJoin('grades as g', 'g.class_subject_id', '=', 'cs.id')
            ->where('subjects.is_active', true)
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('avg_grade')
            ->get();

        // ── At-risk students ──────────────────────────────────────
        $atRiskStudents = DB::table('grades')
            ->select(
                'student_id',
                DB::raw('ROUND(AVG(marks_obtained/total_marks*100),1) as avg_pct'),
                DB::raw('COUNT(*) as exam_count')
            )
            ->groupBy('student_id')
            ->having('avg_pct', '<', 65)
            ->orderBy('avg_pct')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $row->student = DB::table('users')->where('id', $row->student_id)
                    ->select('id', 'name', 'email')->first();
                return $row;
            });

        // ── Attendance heatmap by month ────────────────────────────
        $attendanceHeatmap = DB::table('attendance_records')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as ym'),
                DB::raw('ROUND(SUM(status IN ("present","late"))/COUNT(*)*100, 1) as rate')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('ym')
            ->pluck('rate', 'ym');

        return view('admin.analytics', compact(
            'overallAvg', 'attendanceRate', 'atRiskCount', 'parentEngagement',
            'gradeDistribution', 'monthlyTrend', 'classPerformance',
            'subjectPerformance', 'atRiskStudents', 'attendanceHeatmap', 'year'
        ));
    }
}
