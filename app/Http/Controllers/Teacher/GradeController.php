<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user();

        $classSubjects = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['classroom.gradeLevel','subject'])
            ->get();

        $selectedCs = $request->filled('class_subject_id')
            ? ClassSubject::findOrFail($request->class_subject_id)
            : $classSubjects->first();

        $exams = $selectedCs
            ? Exam::where('class_subject_id', $selectedCs->id)->orderBy('exam_date')->get()
            : collect();

        $selectedExam = $request->filled('exam_id')
            ? Exam::findOrFail($request->exam_id)
            : $exams->first();

        // Get students with their grade for selected exam
        $students = [];
        if ($selectedCs && $selectedExam) {
            $enrollments = Enrollment::where('classroom_id', $selectedCs->classroom_id)
                ->where('status','active')
                ->with(['student:id,name,email',
                    'student.grades' => fn($q) => $q->where('exam_id', $selectedExam->id)])
                ->get();

            $students = $enrollments->map(function($e) use ($selectedExam) {
                $grade = $e->student->grades->first();
                return [
                    'student'        => $e->student,
                    'grade'          => $grade,
                    'marks_obtained' => $grade?->marks_obtained,
                    'percentage'     => $grade ? round($grade->marks_obtained / $selectedExam->total_marks * 100, 1) : null,
                    'letter'         => $grade?->grade_role,
                ];
            });
        }

        return view('teacher.grades.index', compact(
            'classSubjects','selectedCs','exams','selectedExam','students'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_id'          => ['required','exists:exams,id'],
            'class_subject_id' => ['required','exists:class_subjects,id'],
            'grades'           => ['required','array'],
            'grades.*.student_id'     => ['required','exists:users,id'],
            'grades.*.marks_obtained' => ['required','numeric','min:0'],
            'grades.*.is_absent'      => ['boolean'],
            'grades.*.remarks'        => ['nullable','string','max:500'],
        ]);

        $exam    = Exam::findOrFail($request->exam_id);
        $teacher = Auth::user();

        DB::transaction(function () use ($request, $exam, $teacher) {
            foreach ($request->grades as $g) {
                Grade::updateOrCreate(
                    ['student_id' => $g['student_id'], 'exam_id' => $exam->id],
                    [
                        'class_subject_id' => $request->class_subject_id,
                        'graded_by'        => $teacher->id,
                        'marks_obtained'   => $g['is_absent'] ?? false ? 0 : $g['marks_obtained'],
                        'total_marks'      => $exam->total_marks,
                        'is_absent'        => $g['is_absent'] ?? false,
                        'remarks'          => $g['remarks'] ?? null,
                        'graded_at'        => now(),
                    ]
                );
            }
        });

        return back()->with('success', 'Grades saved successfully.');
    }

    public function classReport(ClassSubject $classSubject)
    {
        $this->authorizeTeacher($classSubject);

        $students = Enrollment::where('classroom_id', $classSubject->classroom_id)
            ->where('status','active')
            ->with(['student.grades' => fn($q) => $q->where('class_subject_id', $classSubject->id)
                                                    ->with('exam')])
            ->get();

        $reportData = $students->map(function($e) {
            $grades  = $e->student->grades;
            $average = $grades->count() > 0
                ? round($grades->avg(fn($g) => $g->marks_obtained / $g->total_marks * 100), 1)
                : null;
            return [
                'student' => $e->student,
                'grades'  => $grades,
                'average' => $average,
                'letter'  => $this->letterFromPct($average),
            ];
        })->sortByDesc('average');

        return view('teacher.grades.report', compact('classSubject','reportData'));
    }

    private function authorizeTeacher(ClassSubject $cs): void
    {
        abort_if($cs->teacher_id !== Auth::id(), 403);
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
