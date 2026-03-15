<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $parent   = Auth::user();
        $year     = AcademicYear::current();

        // ── Parent's children ────────────────────────────────────
        $children = $parent->children()->with([
            'studentProfile.classroom.gradeLevel',
        ])->get();

        // Select active child (default: first)
        $activeChildId = $request->get('child_id', $children->first()?->id);
        $child = $children->firstWhere('id', $activeChildId);

        if (!$child) {
            return view('parent.dashboard', compact('children'))->with('noChild', true);
        }

        // ── Child's enrollment ────────────────────────────────────
        $enrollment = Enrollment::where('student_id', $child->id)
            ->where('academic_year_id', $year?->id)
            ->where('status','active')
            ->with(['classroom.gradeLevel','classroom.homeroomTeacher'])
            ->first();

        $classroom = $enrollment?->classroom;

        $classSubjects = $classroom
            ? ClassSubject::where('classroom_id', $classroom->id)
                ->where('academic_year_id', $year?->id)
                ->with(['subject','teacher'])
                ->get()
            : collect();

        // ── Grade summary per subject ────────────────────────────
        $gradesBySubject = Grade::where('student_id', $child->id)
            ->whereIn('class_subject_id', $classSubjects->pluck('id'))
            ->with(['classSubject.subject','exam'])
            ->get()
            ->groupBy('class_subject_id');

        $subjectSummary = $classSubjects->map(function($cs) use ($gradesBySubject) {
            $grades = $gradesBySubject->get($cs->id, collect());
            $avg    = $grades->count() > 0
                ? round($grades->avg(fn($g) => $g->marks_obtained / $g->total_marks * 100), 1)
                : null;
            $latestGrade = $grades->sortByDesc('graded_at')->first();
            return [
                'class_subject' => $cs,
                'subject'       => $cs->subject,
                'teacher'       => $cs->teacher,
                'average'       => $avg,
                'letter'        => $this->letterFromPct($avg),
                'trend'         => $this->gradeTrend($grades),
                'latest_grade'  => $latestGrade,
            ];
        });

        $overallAvg = Grade::where('student_id', $child->id)
            ->avg(DB::raw('marks_obtained/total_marks*100'));

        // ── Attendance ────────────────────────────────────────────
        $attendance = AttendanceRecord::where('student_id', $child->id)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(status="present") as present'),
                DB::raw('SUM(status="absent") as absent'),
                DB::raw('SUM(status="late") as late'),
                DB::raw('ROUND(SUM(status IN ("present","late"))/COUNT(*)*100,1) as rate')
            )->first();

        // ── Class rank ────────────────────────────────────────────
        $rank = null;
        if ($classroom && $overallAvg !== null) {
            $rank = DB::table('grades as g')
                ->join('enrollments as e','e.student_id','=','g.student_id')
                ->where('e.classroom_id', $classroom->id)
                ->where('e.status','active')
                ->select('g.student_id')
                ->groupBy('g.student_id')
                ->havingRaw('AVG(g.marks_obtained/g.total_marks*100) > ?', [$overallAvg])
                ->get()
                ->count() + 1;
        }

        // ── Upcoming exams ────────────────────────────────────────
        $upcomingExams = Exam::whereIn('class_subject_id', $classSubjects->pluck('id'))
            ->where('exam_date', '>=', now()->toDateString())
            ->where('is_published', true)
            ->with('classSubject.subject')
            ->orderBy('exam_date')
            ->limit(5)
            ->get();

        // ── Messages from teachers ────────────────────────────────
        $messages = Message::where('receiver_id', $parent->id)
            ->with('sender:id,name,role')
            ->latest()
            ->limit(5)
            ->get();

        $unreadCount = Message::where('receiver_id', $parent->id)->unread()->count();

        // ── Announcements ─────────────────────────────────────────
        $announcements = Announcement::published()
            ->forAudience('parents')
            ->latest('published_at')
            ->limit(4)
            ->get();

        // ── Recent absence alerts ─────────────────────────────────
        $recentAbsences = AttendanceRecord::where('student_id', $child->id)
            ->where('status','absent')
            ->with('session.classSubject.subject')
            ->latest()
            ->limit(5)
            ->get();

        return view('parent.dashboard', compact(
            'parent', 'children', 'child', 'enrollment', 'classroom',
            'classSubjects', 'subjectSummary', 'overallAvg', 'rank',
            'attendance', 'upcomingExams', 'messages', 'unreadCount',
            'announcements', 'recentAbsences'
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

    private function gradeTrend($grades): string
    {
        if ($grades->count() < 2) return 'stable';
        $sorted = $grades->sortBy('graded_at')->values();
        $first  = $sorted->first()->marks_obtained / $sorted->first()->total_marks;
        $last   = $sorted->last()->marks_obtained  / $sorted->last()->total_marks;
        if ($last > $first + 0.05) return 'up';
        if ($last < $first - 0.05) return 'down';
        return 'stable';
    }
}
