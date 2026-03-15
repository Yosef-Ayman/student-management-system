<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
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

        // Overall summary
        $overall = DB::table('attendance_records as ar')
            ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
            ->where('ar.student_id', $student->id)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(ar.status = "present") as present'),
                DB::raw('SUM(ar.status = "absent") as absent'),
                DB::raw('SUM(ar.status = "late") as late'),
                DB::raw('SUM(ar.status = "excused") as excused'),
                DB::raw('ROUND(SUM(ar.status IN ("present","late"))/COUNT(*)*100,1) as rate')
            )
            ->first();

        // Per-subject attendance
        $bySubject = $classSubjects->map(function ($cs) use ($student) {
            $data = DB::table('attendance_records as ar')
                ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
                ->where('ar.student_id', $student->id)
                ->where('s.class_subject_id', $cs->id)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(ar.status = "present") as present'),
                    DB::raw('SUM(ar.status = "absent") as absent'),
                    DB::raw('SUM(ar.status = "late") as late'),
                    DB::raw('ROUND(SUM(ar.status IN ("present","late"))/COUNT(*)*100,1) as rate')
                )
                ->first();
            return [
                'class_subject' => $cs,
                'subject'       => $cs->subject,
                'teacher'       => $cs->teacher,
                'data'          => $data,
            ];
        });

        // Recent absences
        $recentAbsences = AttendanceRecord::where('student_id', $student->id)
            ->where('status', 'absent')
            ->with(['session.classSubject.subject'])
            ->latest()
            ->limit(10)
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = DB::table('attendance_records as ar')
            ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
            ->where('ar.student_id', $student->id)
            ->where('s.session_date', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(s.session_date, "%b %Y") as month_label'),
                DB::raw('ROUND(SUM(ar.status IN ("present","late"))/COUNT(*)*100,1) as rate')
            )
            ->groupBy(DB::raw('DATE_FORMAT(s.session_date, "%b %Y")'), DB::raw('YEAR(s.session_date)'), DB::raw('MONTH(s.session_date)'))
            ->orderByRaw('YEAR(s.session_date), MONTH(s.session_date)')
            ->get();

        return view('student.attendance.index', compact(
            'student', 'overall', 'bySubject', 'recentAbsences', 'monthlyTrend'
        ));
    }
}
