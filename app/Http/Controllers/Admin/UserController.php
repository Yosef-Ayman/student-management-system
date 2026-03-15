<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withTrashed($request->boolean('trashed'));

        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $roleCounts = [
            'all'     => User::count(),
            'admin'   => User::adminUsers()->count(),
            'teacher' => User::teacherUsers()->count(),
            'student' => User::studentUsers()->count(),
            'parent'  => User::parentUsers()->count(),
        ];

        return view('admin.users.index', compact('users', 'roleCounts'));
    }

    public function create()
    {
        $classrooms = Classroom::active()->with('gradeLevel')->get();
        return view('admin.users.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'min:8', 'confirmed'],
            'role'         => ['required', Rule::in(['admin','teacher','student','parent'])],
            'phone'        => ['nullable', 'string', 'max:20'],
            'gender'       => ['nullable', Rule::in(['male','female'])],
            'date_of_birth'=> ['nullable', 'date'],
            'address'      => ['nullable', 'string'],

            'employee_code'   => ['nullable', 'string', 'unique:teacher_profiles,employee_code'],
            'qualification'   => ['nullable', 'string'],
            'specialization'  => ['nullable', 'string'],
            'hire_date'       => ['nullable', 'date'],

            'student_code'    => ['nullable', 'string', 'unique:student_profiles,student_code'],
            'classroom_id'    => ['nullable', 'exists:classrooms,id'],
            'enrollment_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name'          => $validated['name'],
                'email'         => $validated['email'],
                'password'      => Hash::make($validated['password']),
                'role'          => $validated['role'],
                'phone'         => $validated['phone'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'address'       => $validated['address'] ?? null,
                'is_active'     => true,
            ]);

            match ($user->role) {
                'teacher' => TeacherProfile::create([
                    'user_id'        => $user->id,
                    'employee_code'  => $validated['employee_code'] ?? 'TCH' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'qualification'  => $validated['qualification'] ?? '',
                    'specialization' => $validated['specialization'] ?? '',
                    'hire_date'      => $validated['hire_date'] ?? now()->toDateString(),
                ]),
                'student' => StudentProfile::create([
                    'user_id'         => $user->id,
                    'student_code'    => $validated['student_code'] ?? 'STU' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                    'classroom_id'    => $validated['classroom_id'] ?? null,
                    'enrollment_date' => $validated['enrollment_date'] ?? now()->toDateString(),
                    'status'          => 'active',
                ]),
                'parent' => ParentProfile::create(['user_id' => $user->id]),
                default  => null,
            };
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load([
            'teacherProfile', 'studentProfile.classroom.gradeLevel',
            'parentProfile', 'children.studentProfile',
            'grades.exam.classSubject.subject',
            'enrollments.classroom', 'attendanceRecords',
        ]);

        $gradeAvg = $user->grades()->avg(DB::raw('marks_obtained / total_marks * 100'));

        return view('admin.users.show', compact('user', 'gradeAvg'));
    }

    public function edit(User $user)
    {
        $user->load(['teacherProfile', 'studentProfile', 'parentProfile']);
        $classrooms = Classroom::active()->with('gradeLevel')->get();
        return view('admin.users.edit', compact('user', 'classrooms'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'gender'   => ['nullable', Rule::in(['male','female'])],
            'is_active'=> ['boolean'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deactivated successfully.');
    }

    public function restore(int $id)
    {
        User::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'User restored successfully.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }
}
