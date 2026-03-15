@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

{{-- KPI row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">
                <i class="fas fa-user-graduate" style="color:#7c3aed;"></i>
            </div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_students']) }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;">
                <i class="fas fa-chalkboard-teacher" style="color:#059669;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_teachers'] }}</div>
                <div class="stat-label">Teachers</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <i class="fas fa-school" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_classes'] }}</div>
                <div class="stat-label">Active Classes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <i class="fas fa-calendar-check" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['attendance_rate'] }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Grade Distribution chart --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-bar me-2 text-primary"></i>Grade Distribution</h6>
                <span class="badge bg-light text-muted">This Semester</span>
            </div>
            <div class="card-body p-3">
                <canvas id="gradeChart" height="100"></canvas>
            </div>
        </div>
    </div>
    {{-- Role breakdown --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header-clean">
                <h6><i class="fas fa-users me-2 text-primary"></i>User Breakdown</h6>
            </div>
            <div class="card-body p-3">
                <canvas id="roleChart" height="180"></canvas>
                <div class="mt-3">
                    @foreach([['Students','#7c3aed',$stats['total_students']],['Parents','#059669',$stats['total_parents']],['Teachers','#2563eb',$stats['total_teachers']]] as [$label,$color,$val])
                    <div class="d-flex justify-content-between align-items-center py-1" style="font-size:.83rem;">
                        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:{{$color}};margin-right:6px;"></span>{{$label}}</span>
                        <span class="fw-600">{{ number_format($val) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Recent students --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-user-graduate me-2 text-primary"></i>Recent Students</h6>
                <a href="{{ route('admin.users.index', ['role'=>'student']) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th><th>Class</th><th>Joined</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStudents as $s)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="topbar-avatar" style="width:30px;height:30px;font-size:.7rem;">
                                        {{ strtoupper(substr($s->name,0,2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:500;font-size:.875rem;">{{ $s->name }}</div>
                                        <div style="font-size:.75rem;color:#9ca3af;">{{ $s->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $s->studentProfile?->classroom?->name ?? '—' }}</td>
                            <td style="font-size:.8rem;color:#6b7280;">{{ $s->created_at?->format('d M Y') }}</td>
                            <td>
                                @if($s->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Announcements --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-bullhorn me-2 text-primary"></i>Announcements</h6>
            </div>
            <div class="card-body p-0">
                @forelse($announcements as $ann)
                <div class="p-3 border-bottom">
                    <div style="font-size:.85rem;font-weight:500;color:#1a1d2e;">{{ $ann->title }}</div>
                    <div style="font-size:.78rem;color:#6b7280;margin-top:3px;">
                        <i class="fas fa-user me-1"></i>{{ $ann->author->name }}
                        · {{ $ann->published_at?->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:.85rem;">No announcements.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- At-risk students --}}
@if($atRiskStudents->count())
<div class="card mb-4">
    <div class="card-header-clean">
        <h6><i class="fas fa-exclamation-triangle me-2 text-danger"></i>At-Risk Students (avg &lt; 60%)</h6>
        <a href="{{ route('admin.analytics') }}" class="btn btn-sm btn-outline-danger">Full Analytics</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Student</th><th>Avg Grade</th><th>Exams</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($atRiskStudents as $r)
                <tr>
                    <td>{{ $r->student->name ?? '—' }}</td>
                    <td>
                        <span class="badge bg-danger-subtle text-danger fw-600">{{ $r->avg_pct }}%</span>
                    </td>
                    <td>{{ $r->exam_count }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $r->student_id) }}"
                           class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
const gradeLabels = @json($gradeDistribution->keys());
const gradeCounts = @json($gradeDistribution->values());
const gradeColors = ['#6d28d9','#7c3aed','#2563eb','#059669','#d97706','#dc2626','#6b7280','#ef4444'];

new Chart(document.getElementById('gradeChart'), {
    type: 'bar',
    data: {
        labels: gradeLabels,
        datasets: [{
            label: 'Students',
            data: gradeCounts,
            backgroundColor: gradeColors.slice(0, gradeLabels.length),
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('roleChart'), {
    type: 'doughnut',
    data: {
        labels: ['Students','Parents','Teachers'],
        datasets: [{
            data: [{{ $stats['total_students'] }}, {{ $stats['total_parents'] }}, {{ $stats['total_teachers'] }}],
            backgroundColor: ['#7c3aed','#059669','#2563eb'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '72%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
