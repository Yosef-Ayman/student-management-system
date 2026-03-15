<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function create(Request $request)
    {
        $classrooms   = Classroom::active()->with('gradeLevel')->get();
        $subjects     = Subject::active()->orderBy('name')->get();
        $teachers     = User::teacherUsers()->active()->get();
        $academicYears= AcademicYear::orderByDesc('is_current')->get();
        $preselected  = $request->get('classroom_id');

        return view('admin.class-subjects.create',
            compact('classrooms', 'subjects', 'teachers', 'academicYears', 'preselected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id'     => ['required', 'exists:classrooms,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'teacher_id'       => ['required', 'exists:users,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'schedule'         => ['nullable', 'string', 'max:100'],
            'room_number'      => ['nullable', 'string', 'max:20'],
        ]);

        // Prevent duplicate
        $exists = ClassSubject::where('classroom_id',     $validated['classroom_id'])
            ->where('subject_id',       $validated['subject_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'This subject is already assigned to this classroom for the selected year.']);
        }

        ClassSubject::create(array_merge($validated, ['is_active' => true]));

        return redirect()
            ->route('admin.classrooms.show', $validated['classroom_id'])
            ->with('success', 'Subject assigned successfully.');
    }

    public function edit(ClassSubject $classSubject)
    {
        $teachers = User::teacherUsers()->active()->get();
        return view('admin.class-subjects.edit', compact('classSubject', 'teachers'));
    }

    public function update(Request $request, ClassSubject $classSubject)
    {
        $validated = $request->validate([
            'teacher_id'  => ['required', 'exists:users,id'],
            'schedule'    => ['nullable', 'string', 'max:100'],
            'room_number' => ['nullable', 'string', 'max:20'],
            'is_active'   => ['boolean'],
        ]);

        $classSubject->update($validated);

        return redirect()
            ->route('admin.classrooms.show', $classSubject->classroom_id)
            ->with('success', 'Class subject updated successfully.');
    }

    public function destroy(ClassSubject $classSubject)
    {
        $classroomId = $classSubject->classroom_id;
        $classSubject->delete();

        return redirect()
            ->route('admin.classrooms.show', $classroomId)
            ->with('success', 'Subject removed from classroom.');
    }
}
