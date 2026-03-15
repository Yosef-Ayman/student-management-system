<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $parent   = Auth::user();
        $year     = AcademicYear::current();
        $children = $parent->children()->get();

        $activeChildId = $request->get('child_id', $children->first()?->id);
        $child = $children->firstWhere('id', $activeChildId);

        $subjectSummary = collect();
        $overallAvg     = null;
        $allGrades      = collect();

        if ($child) {
            $enrollment = Enrollment::where('student_id', $child->id)
                ->where('academic_year_id', $year?->id)
                ->where('status', 'active')
                ->with('classroom')
                ->first();

            $classSubjects = $enrollment
                ? ClassSubject::where('classroom_id', $enrollment->classroom_id)
                    ->where('academic_year_id', $year?->id)
                    ->with(['subject', 'teacher'])
                    ->get()
                : collect();

            $gradesByCs = Grade::where('student_id', $child->id)
                ->whereIn('class_subject_id', $classSubjects->pluck('id'))
                ->with(['exam', 'classSubject.subject'])
                ->orderBy('graded_at', 'desc')
                ->get()
                ->groupBy('class_subject_id');

            $subjectSummary = $classSubjects->map(function ($cs) use ($gradesByCs) {
                $grades = $gradesByCs->get($cs->id, collect());
                $avg    = $grades->count() > 0
                    ? round($grades->avg(fn($g) => $g->total_marks > 0 ? $g->marks_obtained / $g->total_marks * 100 : 0), 1)
                    : null;
                return [
                    'class_subject' => $cs,
                    'subject'       => $cs->subject,
                    'teacher'       => $cs->teacher,
                    'grades'        => $grades,
                    'average'       => $avg,
                    'letter'        => $this->letterFromPct($avg),
                ];
            });

            $overallAvg = Grade::where('student_id', $child->id)
                ->whereIn('class_subject_id', $classSubjects->pluck('id'))
                ->avg(DB::raw('marks_obtained / total_marks * 100'));

            $allGrades = $gradesByCs->flatten()->sortByDesc('graded_at')->take(20);
        }

        return view('parent.grades.index', compact(
            'parent', 'children', 'child', 'subjectSummary', 'overallAvg', 'allGrades', 'year'
        ));
    }

    private function letterFromPct(?float $pct): string
    {
        if ($pct === null) return '—';
        return match (true) {
            $pct >= 95 => 'Excellent', $pct >= 90 => 'Very Good',
            $pct >= 80 => 'Good', $pct >= 70 => 'Average Fair',
            $pct >= 60 => 'Pass', default     => 'Failure',
        };
    }
}
