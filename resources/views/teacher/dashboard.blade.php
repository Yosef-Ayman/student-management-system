@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('page-title', 'Teacher Dashboard')

@section('content')

{{-- KPI row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <i class="fas fa-user-graduate" style="color:#059669;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalStudents }}</div>
                <div class="stat-label">My Students</div>
                <div class="stat-change" style="color:#6b7280;">{{ $classSubjects->count() }} classes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <i class="fas fa-chart-line" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-value">{{ round($avgGrade ?? 0, 1) }}%</div>
                <div class="stat-label">Avg Grade</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <i class="fas fa-calendar-check" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $attendanceRate }}%</div>
                <div class="stat-label">Attendance</div>
                <div class="stat-change" style="color:#6b7280;">This week</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;">
                <i class="fas fa-exclamation-triangle" style="color:#dc2626;"></i>
            </div>
            <div>
                <div class="stat-value" style="color:#dc2626;">{{ $atRiskCount }}</div>
                <div class="stat-label">At-Risk Students</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- My classes --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header-clean">
                <h6><i class="fas fa-school me-2 text-success"></i>My Classes</h6>
            </div>
            <div class="card-body p-0">
                @forelse($classSubjects as $cs)
                <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                    <div class="stat-icon" style="background:#d1fae5;width:40px;height:40px;">
                        <i class="fas fa-book" style="color:#059669;font-size:.85rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div style="font-weight:600;font-size:.875rem;">{{ $cs->subject->name }}</div>
                        <div style="font-size:.78rem;color:#6b7280;">
                            {{ $cs->classroom->name }} · {{ $cs->schedule ?? 'No schedule' }}
                        </div>
                    </div>
                    <a href="{{ route('teacher.grades.index', ['class_subject_id'=>$cs->id]) }}"
                       class="btn btn-sm btn-outline-success">Grades</a>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">No classes assigned.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Grade distribution chart --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-pie me-2 text-success"></i>Grade Distribution</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="gradeDistChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Upcoming exams --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header-clean">
                <h6><i class="fas fa-calendar-alt me-2 text-success"></i>Upcoming Exams</h6>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingExams as $exam)
                <div class="p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:.85rem;font-weight:500;">{{ $exam->classSubject->subject->name }}</div>
                            <div style="font-size:.78rem;color:#6b7280;">
                                {{ $exam->classSubject->classroom->name }} · {{ ucfirst($exam->type) }}
                            </div>
                        </div>
                        <span class="badge bg-warning-subtle text-warning" style="font-size:.75rem;">
                            {{ $exam->exam_date->format('d M') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">No upcoming exams.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- At-risk students --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-exclamation-triangle me-2 text-danger"></i>At-Risk Students</h6>
                <a href="{{ route('teacher.attendance.summary') }}" class="btn btn-sm btn-outline-danger">Full Report</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Student</th><th>Avg</th><th>Exams</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        @forelse($atRiskStudents as $r)
                        <tr>
                            <td style="font-size:.875rem;">{{ $r->student->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $r->avg_pct < 50 ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }} fw-600">
                                    {{ $r->avg_pct }}%
                                </span>
                            </td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $r->exams_taken }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary">Contact Parent</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No at-risk students.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent grades entered --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-clipboard-check me-2 text-success"></i>Recently Graded</h6>
                <a href="{{ route('teacher.grades.index') }}" class="btn btn-sm btn-outline-success">All Grades</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Student</th><th>Subject</th><th>Score</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentGrades as $g)
                        @php $pct = round($g->marks_obtained / $g->total_marks * 100, 1); @endphp
                        <tr>
                            <td style="font-size:.875rem;">{{ $g->student->name }}</td>
                            <td style="font-size:.83rem;color:#6b7280;">{{ $g->exam->classSubject->subject->name }}</td>
                            <td>
                                <span class="badge fw-600"
                                    style="background:{{ $pct>=85?'#d1fae5':($pct>=70?'#dbeafe':($pct>=60?'#fef3c7':'#fee2e2')) }};
                                           color:{{ $pct>=85?'#065f46':($pct>=70?'#1e40af':($pct>=60?'#92400e':'#991b1b')) }};">
                                    {{ $pct }}%
                                </span>
                            </td>
                            <td style="font-size:.78rem;color:#9ca3af;">{{ $g->graded_at->format('d M') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No grades entered yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Unread messages + Announcements --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-envelope me-2 text-success"></i>Unread Messages
                    @if($unreadMessages->count())
                    <span class="badge bg-danger ms-1">{{ $unreadMessages->count() }}</span>
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                @forelse($unreadMessages as $msg)
                <div class="d-flex align-items-start gap-3 p-3 border-bottom">
                    <div class="topbar-avatar" style="width:34px;height:34px;font-size:.75rem;flex-shrink:0;">
                        {{ strtoupper(substr($msg->sender->name,0,2)) }}
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div style="font-size:.85rem;font-weight:600;">{{ $msg->sender->name }}</div>
                        <div style="font-size:.8rem;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $msg->subject ?? Str::limit($msg->body, 50) }}
                        </div>
                        <div style="font-size:.72rem;color:#9ca3af;margin-top:2px;">{{ $msg->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">
                    <i class="fas fa-inbox d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>No unread messages
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-bullhorn me-2 text-success"></i>Announcements</h6>
            </div>
            <div class="card-body p-0">
                @forelse($announcements as $ann)
                <div class="p-3 border-bottom">
                    <div style="font-size:.85rem;font-weight:500;">{{ $ann->title }}</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:3px;">
                        {{ $ann->published_at?->diffForHumans() }}
                        · <span class="badge bg-light text-muted">{{ ucfirst($ann->audience) }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">No announcements.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
const labels = @json($gradeDistribution->keys());
const data   = @json($gradeDistribution->values());
const colors = ['#6d28d9','#2563eb','#059669','#d97706','#dc2626'];

new Chart(document.getElementById('gradeDistChart'), {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{ data: data, backgroundColor: colors.slice(0, labels.length), borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
    }
});
</script>
@endpush
