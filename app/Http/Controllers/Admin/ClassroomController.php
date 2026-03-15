<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::with(['gradeLevel', 'homeroomTeacher', 'academicYear'])
            ->withCount(['enrollments as student_count' => fn($q) => $q->where('status','active')])
            ->latest()
            ->paginate(20);

        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $gradeLevels  = GradeLevel::orderBy('level')->get();
        $academicYears= AcademicYear::orderByDesc('is_current')->get();
        $teachers     = User::teacherUsers()->active()->get();
        return view('admin.classrooms.create', compact('gradeLevels','academicYears','teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:100'],
            'grade_level_id'      => ['required', 'exists:grade_levels,id'],
            'academic_year_id'    => ['required', 'exists:academic_years,id'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'capacity'            => ['required', 'integer', 'min:1', 'max:100'],
            'room_number'         => ['nullable', 'string', 'max:20'],
        ]);

        Classroom::create(array_merge($validated, ['is_active' => true]));

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom created successfully.');
    }

    public function show(Classroom $classroom)
    {
        $classroom->load([
            'gradeLevel', 'academicYear', 'homeroomTeacher',
            'classSubjects.subject', 'classSubjects.teacher',
            'enrollments.student.studentProfile',
        ]);

        $studentCount = $classroom->enrollments->where('status','active')->count();

        return view('admin.classrooms.show', compact('classroom', 'studentCount'));
    }

    public function edit(Classroom $classroom)
    {
        $gradeLevels  = GradeLevel::orderBy('level')->get();
        $academicYears= AcademicYear::orderByDesc('is_current')->get();
        $teachers     = User::teacherUsers()->active()->get();
        return view('admin.classrooms.edit', compact('classroom','gradeLevels','academicYears','teachers'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:100'],
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
            'capacity'            => ['required', 'integer', 'min:1'],
            'room_number'         => ['nullable', 'string', 'max:20'],
            'is_active'           => ['boolean'],
        ]);

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.show', $classroom)
            ->with('success', 'Classroom updated successfully.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->update(['is_active' => false]);
        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Classroom deactivated.');
    }

    public function enroll(Request $request, Classroom $classroom)
    {
        $request->validate([
            'student_id' => ['required', 'exists:users,id'],
        ]);

        $year = AcademicYear::current();

        Enrollment::firstOrCreate(
            ['student_id' => $request->student_id, 'classroom_id' => $classroom->id, 'academic_year_id' => $year->id],
            ['enrolled_at' => now()->toDateString(), 'status' => 'active']
        );

        return back()->with('success', 'Student enrolled successfully.');
    }
}
