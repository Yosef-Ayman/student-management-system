@extends('layouts.app')
@section('title', $user->name)
@section('page-title', 'User Profile')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.users.index', ['role' => $user->role]) }}"
       class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-edit me-1"></i>Edit
    </a>
</div>

<div class="row g-3">
    {{-- Profile card --}}
    <div class="col-md-4">
        <div class="card text-center p-4 mb-3">
            <div class="topbar-avatar mx-auto mb-3"
                 style="width:64px;height:64px;font-size:1.3rem;background:#4f46e5;">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h5 class="fw-600 mb-1">{{ $user->name }}</h5>
            <div style="font-size:.85rem;color:#6b7280;">{{ $user->email }}</div>
            <div class="mt-2">
                <span class="badge role-{{ $user->role }}" style="font-size:.8rem;padding:5px 14px;">
                    {{ ucfirst($user->role) }}
                </span>
                @if($user->is_active)
                    <span class="badge bg-success-subtle text-success ms-1">Active</span>
                @else
                    <span class="badge bg-danger-subtle text-danger ms-1">Inactive</span>
                @endif
            </div>
        </div>

        <div class="card p-3">
            <h6 class="fw-600 mb-3" style="font-size:.875rem;">Details</h6>
            @foreach([
                ['Phone',    $user->phone ?? '—'],
                ['Gender',   ucfirst($user->gender ?? '—')],
                ['DOB',      $user->date_of_birth?->format('d M Y') ?? '—'],
                ['Joined',   $user->created_at?->format('d M Y') ?? '—'],
            ] as [$label, $val])
            <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                <span class="text-muted">{{ $label }}</span>
                <span class="fw-500">{{ $val }}</span>
            </div>
            @endforeach

            @if($user->isTeacher() && $user->teacherProfile)
            <div class="mt-3 pt-2">
                <div class="fw-600 mb-2" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;">Teacher Info</div>
                @foreach([
                    ['Code',     $user->teacherProfile->employee_code],
                    ['Subject',  $user->teacherProfile->specialization],
                    ['Hire Date',$user->teacherProfile->hire_date?->format('d M Y') ?? '—'],
                ] as [$l,$v])
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                    <span class="text-muted">{{ $l }}</span><span class="fw-500">{{ $v }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if($user->isStudent() && $user->studentProfile)
            <div class="mt-3 pt-2">
                <div class="fw-600 mb-2" style="font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;">Student Info</div>
                @foreach([
                    ['Code',     $user->studentProfile->student_code],
                    ['Class',    $user->studentProfile->classroom?->name ?? '—'],
                    ['Status',   ucfirst($user->studentProfile->status)],
                    ['Avg Grade', $gradeAvg ? round($gradeAvg, 1).'%' : '—'],
                ] as [$l,$v])
                <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.83rem;">
                    <span class="text-muted">{{ $l }}</span><span class="fw-500">{{ $v }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Right column --}}
    <div class="col-md-8">
        @if($user->isParent())
        <div class="card mb-3">
            <div class="card-header-clean">
                <h6><i class="fas fa-user-graduate me-2 text-warning"></i>Children</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Class</th><th>Relation</th></tr>
                    </thead>
                    <tbody>
                        @forelse($user->children as $child)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $child) }}" style="font-size:.875rem;">
                                    {{ $child->name }}
                                </a>
                            </td>
                            <td style="font-size:.83rem;color:#6b7280;">
                                {{ $child->studentProfile?->classroom?->name ?? '—' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-muted">{{ $child->pivot->relation }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No children linked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($user->isStudent() && $user->grades->count())
        <div class="card mb-3">
            <div class="card-header-clean">
                <h6><i class="fas fa-star me-2 text-primary"></i>Recent Grades</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Subject</th><th>Exam</th><th>Score</th><th>Grade</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach($user->grades->take(10) as $g)
                        @php $pct = $g->total_marks > 0 ? round($g->marks_obtained / $g->total_marks * 100, 1) : 0; @endphp
                        <tr>
                            <td style="font-size:.875rem;">{{ $g->exam?->classSubject?->subject?->name ?? '—' }}</td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $g->exam?->title ?? '—' }}</td>
                            <td style="font-size:.83rem;">{{ $g->marks_obtained }} / {{ $g->total_marks }}</td>
                            <td>
                                <span class="badge fw-600"
                                    style="background:{{ $pct>=85?'#d1fae5':($pct>=70?'#dbeafe':($pct>=60?'#fef3c7':'#fee2e2')) }};
                                           color:{{ $pct>=85?'#065f46':($pct>=70?'#1e40af':($pct>=60?'#92400e':'#991b1b')) }};">
                                    {{ $pct }}%
                                </span>
                            </td>
                            <td style="font-size:.78rem;color:#9ca3af;">
                                {{ $g->graded_at?->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($user->enrollments->count())
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-school me-2 text-primary"></i>Enrollments</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Classroom</th><th>Year</th><th>Enrolled</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($user->enrollments as $e)
                        <tr>
                            <td style="font-size:.875rem;">{{ $e->classroom?->name ?? '—' }}</td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $e->academicYear?->name ?? '—' }}</td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $e->enrolled_at?->format('d M Y') ?? '—' }}</td>
                            <td><span class="badge bg-success-subtle text-success">{{ ucfirst($e->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
