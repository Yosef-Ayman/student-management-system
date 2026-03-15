<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $parent   = Auth::user();
        $year     = AcademicYear::current();
        $children = $parent->children()->get();

        $activeChildId = $request->get('child_id', $children->first()?->id);
        $child = $children->firstWhere('id', $activeChildId);

        $overall        = null;
        $bySubject      = collect();
        $recentAbsences = collect();
        $monthlyTrend   = collect();

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

            $overall = DB::table('attendance_records as ar')
                ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
                ->where('ar.student_id', $child->id)
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(ar.status = "present") as present'),
                    DB::raw('SUM(ar.status = "absent") as absent'),
                    DB::raw('SUM(ar.status = "late") as late'),
                    DB::raw('ROUND(SUM(ar.status IN ("present","late"))/COUNT(*)*100,1) as rate')
                )
                ->first();

            $bySubject = $classSubjects->map(function ($cs) use ($child) {
                $data = DB::table('attendance_records as ar')
                    ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
                    ->where('ar.student_id', $child->id)
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

            $recentAbsences = AttendanceRecord::where('student_id', $child->id)
                ->where('status', 'absent')
                ->with(['session.classSubject.subject'])
                ->latest()
                ->limit(10)
                ->get();

            $monthlyTrend = DB::table('attendance_records as ar')
                ->join('attendance_sessions as s', 's.id', '=', 'ar.session_id')
                ->where('ar.student_id', $child->id)
                ->where('s.session_date', '>=', now()->subMonths(6))
                ->select(
                    DB::raw('DATE_FORMAT(s.session_date, "%b %Y") as month_label'),
                    DB::raw('ROUND(SUM(ar.status IN ("present","late"))/COUNT(*)*100,1) as rate')
                )
                ->groupBy(DB::raw('DATE_FORMAT(s.session_date, "%b %Y")'), DB::raw('YEAR(s.session_date)'), DB::raw('MONTH(s.session_date)'))
                ->orderByRaw('YEAR(s.session_date), MONTH(s.session_date)')
                ->get();
        }

        return view('parent.attendance.index', compact(
            'parent', 'children', 'child', 'overall',
            'bySubject', 'recentAbsences', 'monthlyTrend'
        ));
    }
}
