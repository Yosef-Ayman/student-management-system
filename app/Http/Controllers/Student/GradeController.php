<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $year    = AcademicYear::current();

        $enrollment = Enrollment::where('student_id', $student->id)
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

        // All grades grouped by class_subject_id
        $allGrades = Grade::where('student_id', $student->id)
            ->whereIn('class_subject_id', $classSubjects->pluck('id'))
            ->with(['exam', 'classSubject.subject'])
            ->orderBy('graded_at', 'desc')
            ->get()
            ->groupBy('class_subject_id');

        // Summary per subject
        $subjectSummary = $classSubjects->map(function ($cs) use ($allGrades) {
            $grades  = $allGrades->get($cs->id, collect());
            $avg     = $grades->count() > 0
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

        $overallAvg = Grade::where('student_id', $student->id)
            ->whereIn('class_subject_id', $classSubjects->pluck('id'))
            ->avg(DB::raw('marks_obtained / total_marks * 100'));

        return view('student.grades.index', compact(
            'student', 'subjectSummary', 'overallAvg', 'year'
        ));
    }

    private function letterFromPct(?float $pct): string
    {
        if ($pct === null) return '—';
        return match (true) {
            $pct >= 95 => 'Excellent', $pct >= 90 => 'Very Good',
            $pct >= 80 => 'Good', $pct >= 70 => 'Average Fair',
            $pct >= 60 => 'Pass', default => 'Failure',
        };
    }
}
