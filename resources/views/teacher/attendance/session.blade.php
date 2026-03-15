@extends('layouts.app')
@section('title', 'Session Detail')
@section('page-title', 'Session Detail')

@section('content')

    <div class="mb-4">
        <a href="{{ route('teacher.attendance.index', ['class_subject_id' => $session->classSubject->id]) }}"
           class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card">
        <div class="card-header-clean">
            <h6>
                <i class="fas fa-calendar-day me-2 text-success"></i>
                {{ $session->session_date->format('l, d M Y') }}
                — {{ $session->classSubject->subject->name }}
                ({{ $session->classSubject->classroom->name }})
            </h6>
            <div class="d-flex gap-2">
            <span class="badge bg-success-subtle text-success">
                {{ $session->records->where('status','present')->count() }} present
            </span>
                <span class="badge bg-danger-subtle text-danger">
                {{ $session->records->where('status','absent')->count() }} absent
            </span>
                <span class="badge bg-warning-subtle text-warning">
                {{ $session->records->where('status','late')->count() }} late
            </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr><th>#</th><th>Student</th><th>Status</th><th>Late (min)</th><th>Parent Notified</th></tr>
                </thead>
                <tbody>
                @foreach($session->records as $i => $rec)
                    <tr>
                        <td style="color:#9ca3af;font-size:.83rem;">{{ $i + 1 }}</td>
                        <td style="font-weight:500;font-size:.875rem;">{{ $rec->student->name }}</td>
                        <td>
                            @php
                                $colors = ['present'=>'success','absent'=>'danger','late'=>'warning','excused'=>'secondary'];
                                $c = $colors[$rec->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $c }}-subtle text-{{ $c }}">
                            {{ ucfirst($rec->status) }}
                        </span>
                        </td>
                        <td style="font-size:.83rem;color:#6b7280;">
                            {{ $rec->minutes_late > 0 ? $rec->minutes_late.' min' : '—' }}
                        </td>
                        <td>
                            @if($rec->parent_notified)
                                <span class="badge bg-success-subtle text-success">
                                <i class="fas fa-check me-1"></i>Notified
                            </span>
                            @else
                                <span class="text-muted" style="font-size:.8rem;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
