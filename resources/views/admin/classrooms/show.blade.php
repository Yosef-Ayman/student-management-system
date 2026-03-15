@extends('layouts.app')
@section('title', $classroom->name)
@section('page-title', 'Classroom Detail')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.classrooms.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.class-subjects.create', ['classroom_id' => $classroom->id]) }}"
               class="btn btn-sm btn-outline-success">
                <i class="fas fa-plus me-1"></i>Assign Subject
            </a>
            <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Info card --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header-clean">
                    <h6><i class="fas fa-school me-2 text-primary"></i>{{ $classroom->name }}</h6>
                    @if($classroom->is_active)
                        <span class="badge bg-success-subtle text-success">Active</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @foreach([
                        ['Grade Level',      $classroom->gradeLevel->name],
                        ['Academic Year',    $classroom->academicYear->name],
                        ['Room Number',      $classroom->room_number ?? '—'],
                        ['Capacity',         $classroom->capacity.' seats'],
                        ['Enrolled',         $studentCount.' students'],
                        ['Homeroom Teacher', $classroom->homeroomTeacher?->name ?? '—'],
                    ] as [$label, $val])
                        <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                            <span class="text-muted">{{ $label }}</span>
                            <span class="fw-500">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Enroll student --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header-clean">
                    <h6><i class="fas fa-user-plus me-2 text-success"></i>Enroll Student</h6>
                </div>
                <div class="card-body p-3">
                    <form method="POST" action="{{ route('admin.classrooms.enroll', $classroom) }}">
                        @csrf
                        <label class="form-label" style="font-size:.83rem;font-weight:500;">Select Student</label>
                        <select name="student_id" class="form-select form-select-sm mb-3" required>
                            <option value="">Choose a student...</option>
                            @foreach(\App\Models\User::studentUsers()->active()->get() as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-plus me-2"></i>Enroll in this Classroom
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header-clean">
                    <h6><i class="fas fa-chart-bar me-2 text-primary"></i>Stats</h6>
                </div>
                <div class="card-body p-3">
                    @php $pct = $classroom->capacity > 0 ? round($studentCount / $classroom->capacity * 100) : 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size:.83rem;">
                            <span class="text-muted">Capacity used</span>
                            <span class="fw-600">{{ $pct }}%</span>
                        </div>
                        <div style="background:#f1f5f9;border-radius:6px;height:8px;">
                            <div style="width:{{ $pct }}%;background:{{ $pct>=90?'#ef4444':($pct>=70?'#f59e0b':'#059669') }};border-radius:6px;height:100%;transition:width .3s;"></div>
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;margin-top:4px;">{{ $studentCount }} / {{ $classroom->capacity }}</div>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                        <span class="text-muted">Subjects taught</span>
                        <span class="fw-600">{{ $classroom->classSubjects->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2" style="font-size:.83rem;">
                        <span class="text-muted">Teachers assigned</span>
                        <span class="fw-600">{{ $classroom->classSubjects->pluck('teacher_id')->unique()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Class Subjects --}}
    <div class="card mb-4">
        <div class="card-header-clean">
            <h6><i class="fas fa-book me-2 text-success"></i>Subjects & Teachers</h6>
            <a href="{{ route('admin.class-subjects.create', ['classroom_id' => $classroom->id]) }}"
               class="btn btn-sm btn-outline-success">
                <i class="fas fa-plus me-1"></i>Assign Subject
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr><th>Subject</th><th>Teacher</th><th>Schedule</th><th>Actions</th></tr>
                </thead>
                <tbody>
                @forelse($classroom->classSubjects as $cs)
                    <tr>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $cs->subject->name }}</div>
                            <div style="font-size:.75rem;color:#9ca3af;">{{ $cs->subject->code }}</div>
                        </td>
                        <td style="font-size:.875rem;">{{ $cs->teacher->name }}</td>
                        <td style="font-size:.83rem;color:#6b7280;">{{ $cs->schedule ?? '—' }}</td>
                        <td>
                            <a href="{{ route('admin.class-subjects.edit', $cs) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3" style="font-size:.85rem;">
                            No subjects assigned yet.
                            <a href="{{ route('admin.class-subjects.create', ['classroom_id' => $classroom->id]) }}">Assign one</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Enrolled Students --}}
    <div class="card">
        <div class="card-header-clean">
            <h6><i class="fas fa-users me-2 text-primary"></i>Enrolled Students</h6>
            <span class="badge bg-light text-muted">{{ $studentCount }} active</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr><th>Student</th><th>Student Code</th><th>Enrolled</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                @forelse($classroom->enrollments as $e)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="topbar-avatar" style="width:30px;height:30px;font-size:.7rem;background:#2563eb;">
                                    {{ strtoupper(substr($e->student->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500;font-size:.875rem;">{{ $e->student->name }}</div>
                                    <div style="font-size:.75rem;color:#9ca3af;">{{ $e->student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:.83rem;color:#6b7280;">
                            {{ $e->student->studentProfile?->student_code ?? '—' }}
                        </td>
                        <td style="font-size:.8rem;color:#6b7280;">{{ $e->enrolled_at->format('d M Y') }}</td>
                        <td>
                        <span class="badge {{ $e->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                            {{ ucfirst($e->status) }}
                        </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $e->student_id) }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3" style="font-size:.85rem;">
                            No students enrolled yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
