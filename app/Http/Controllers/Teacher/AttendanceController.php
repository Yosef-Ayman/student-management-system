<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
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

        $sessions = $selectedCs
            ? AttendanceSession::where('class_subject_id', $selectedCs->id)
                ->withCount(['records',
                    'records as present_count' => fn($q) => $q->where('status','present'),
                    'records as absent_count'  => fn($q) => $q->where('status','absent'),
                    'records as late_count'    => fn($q) => $q->where('status','late'),
                ])
                ->orderByDesc('session_date')
                ->paginate(15)
            : collect();

        // Today's attendance form students
        $students = [];
        if ($selectedCs) {
            $today   = now()->toDateString();
            $session = AttendanceSession::where('class_subject_id', $selectedCs->id)
                ->where('session_date', $today)->first();

            $enrollments = Enrollment::where('classroom_id', $selectedCs->classroom_id)
                ->where('status','active')
                ->with('student:id,name,email')
                ->get();

            $students = $enrollments->map(function($e) use ($session) {
                $record = $session
                    ? AttendanceRecord::where('session_id', $session->id)
                        ->where('student_id', $e->student_id)->first()
                    : null;
                return [
                    'student' => $e->student,
                    'record'  => $record,
                    'status'  => $record?->status ?? 'present',
                ];
            });
        }

        return view('teacher.attendance.index', compact(
            'classSubjects','selectedCs','sessions','students'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_subject_id' => ['required','exists:class_subjects,id'],
            'session_date'     => ['required','date'],
            'topic'            => ['nullable','string','max:255'],
            'records'          => ['required','array'],
            'records.*.student_id' => ['required','exists:users,id'],
            'records.*.status'     => ['required','in:present,absent,late,excused'],
            'records.*.minutes_late'=> ['nullable','integer','min:0'],
        ]);

        $cs = ClassSubject::findOrFail($request->class_subject_id);
        abort_if($cs->teacher_id !== Auth::id(), 403);

        DB::transaction(function () use ($request, $cs) {
            $session = AttendanceSession::updateOrCreate(
                ['class_subject_id' => $cs->id, 'session_date' => $request->session_date],
                ['taken_by' => Auth::id(), 'topic' => $request->topic]
            );

            foreach ($request->records as $rec) {
                $record = AttendanceRecord::updateOrCreate(
                    ['session_id' => $session->id, 'student_id' => $rec['student_id']],
                    [
                        'status'          => $rec['status'],
                        'minutes_late'    => $rec['minutes_late'] ?? 0,
                        'excuse_reason'   => $rec['excuse_reason'] ?? null,
                    ]
                );

                // Notify parent if absent and not yet notified
                if ($rec['status'] === 'absent' && !$record->parent_notified) {
                    // Dispatch notification job (stub — hook into your notification system)
                    $record->update(['parent_notified' => true, 'notified_at' => now()]);
                }
            }
        });

        return back()->with('success', 'Attendance recorded successfully.');
    }

    public function sessionDetail(AttendanceSession $session)
    {
        abort_if($session->classSubject->teacher_id !== Auth::id(), 403);

        $session->load([
            'classSubject.subject',
            'classSubject.classroom.gradeLevel',
            'records.student:id,name,email',
        ]);

        return view('teacher.attendance.session', compact('session'));
    }

    public function studentSummary(Request $request)
    {
        $teacher = Auth::user();
        $csIds   = ClassSubject::where('teacher_id', $teacher->id)->pluck('id');

        // Use DB::table with JOIN to get student name — avoids Eloquent scope issues
        $summary = DB::table('attendance_records as ar')
            ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
            ->join('users as u', 'u.id', '=', 'ar.student_id')
            ->whereIn('s.class_subject_id', $csIds)
            ->select(
                'ar.student_id',
                'u.name as student_name',
                'u.email as student_email',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(ar.status = "present") as present'),
                DB::raw('SUM(ar.status = "absent") as absent'),
                DB::raw('SUM(ar.status = "late") as late'),
                DB::raw('ROUND(SUM(ar.status IN ("present","late"))/COUNT(*)*100,1) as rate')
            )
            ->groupBy('ar.student_id', 'u.name', 'u.email')
            ->orderBy('rate')
            ->paginate(20);

        return view('teacher.attendance.summary', compact('summary'));
    }
}
