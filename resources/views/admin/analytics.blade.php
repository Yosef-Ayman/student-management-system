@extends('layouts.app')
@section('title', 'Analytics & Reports')
@section('page-title', 'Analytics & Reports')

@section('content')

{{-- Filter bar --}}
<div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
    <span style="font-size:.85rem;color:#6b7280;">Academic Year:</span>
    <span class="badge bg-primary-subtle text-primary" style="font-size:.83rem;padding:6px 12px;">
        {{ $year?->name ?? 'Current' }}
    </span>
    <div class="ms-auto d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-pdf me-1"></i>Export PDF</button>
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-excel me-1"></i>Export Excel</button>
    </div>
</div>

{{-- KPI row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;"><i class="fas fa-chart-line" style="color:#7c3aed;"></i></div>
            <div>
                <div class="stat-value">{{ round($overallAvg ?? 0, 1) }}%</div>
                <div class="stat-label">Overall Avg Grade</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;"><i class="fas fa-calendar-check" style="color:#059669;"></i></div>
            <div>
                <div class="stat-value">{{ $attendanceRate }}%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;"><i class="fas fa-exclamation-triangle" style="color:#dc2626;"></i></div>
            <div>
                <div class="stat-value" style="color:#dc2626;">{{ $atRiskCount }}</div>
                <div class="stat-label">At-Risk Students</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;"><i class="fas fa-school" style="color:#2563eb;"></i></div>
            <div>
                <div class="stat-value">{{ $classPerformance->count() }}</div>
                <div class="stat-label">Active Classes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;"><i class="fas fa-users" style="color:#d97706;"></i></div>
            <div>
                <div class="stat-value">{{ $parentEngagement }}</div>
                <div class="stat-label">Parent Engaged</div>
            </div>
        </div>
    </div>
</div>

{{-- Trend chart + grade distribution --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-line me-2 text-primary"></i>Grade & Attendance Trend (Last 6 Months)</h6>
            </div>
            <div class="card-body"><canvas id="trendChart" height="100"></canvas></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-chart-pie me-2 text-primary"></i>Grade Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="gradePie" height="180"></canvas>
                <div class="mt-2">
                    @foreach($gradeDistribution as $letter => $count)
                    <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:.82rem;">
                        <span>{{ $letter }}</span>
                        <span class="fw-600">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Class performance + Subject performance --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-school me-2 text-primary"></i>Performance by Class</h6>
            </div>
            <div class="card-body"><canvas id="classChart" height="200"></canvas></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header-clean">
                <h6><i class="fas fa-book me-2 text-primary"></i>Performance by Subject</h6>
            </div>
            <div class="card-body"><canvas id="subjectChart" height="200"></canvas></div>
        </div>
    </div>
</div>

{{-- At-risk students table --}}
<div class="card mb-4">
    <div class="card-header-clean">
        <h6><i class="fas fa-exclamation-triangle me-2 text-danger"></i>At-Risk Students (avg &lt; 65%)</h6>
        <span class="badge bg-danger-subtle text-danger">{{ $atRiskStudents->count() }} students</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Student</th><th>Avg Grade</th><th>Exams Taken</th><th>Risk Level</th><th>Action</th></tr>
            </thead>
            <tbody>
                @forelse($atRiskStudents as $i => $r)
                <tr>
                    <td style="color:#9ca3af;font-size:.83rem;">{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:500;font-size:.875rem;">{{ $r->student->name ?? '—' }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $r->student->email ?? '' }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:80px;background:#f1f5f9;border-radius:4px;height:6px;">
                                <div style="width:{{ $r->avg_pct }}%;background:{{ $r->avg_pct<50?'#dc2626':'#d97706' }};border-radius:4px;height:100%;"></div>
                            </div>
                            <span class="fw-600" style="font-size:.83rem;color:{{ $r->avg_pct<50?'#dc2626':'#d97706' }};">{{ $r->avg_pct }}%</span>
                        </div>
                    </td>
                    <td style="font-size:.83rem;color:#6b7280;">{{ $r->exam_count }}</td>
                    <td>
                        @if($r->avg_pct < 50)
                            <span class="badge bg-danger-subtle text-danger">Critical</span>
                        @elseif($r->avg_pct < 60)
                            <span class="badge bg-warning-subtle text-warning">High Risk</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Monitor</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $r->student_id) }}"
                           class="btn btn-sm btn-outline-secondary">View Profile</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No at-risk students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Attendance heatmap --}}
<div class="card">
    <div class="card-header-clean">
        <h6><i class="fas fa-th me-2 text-primary"></i>Monthly Attendance Heatmap</h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach($attendanceHeatmap as $ym => $rate)
            @php
                $bg = $rate >= 80 ? '#d1fae5' : ($rate >= 70 ? '#fef3c7' : '#fee2e2');
                $fg = $rate >= 80 ? '#065f46' : ($rate >= 70 ? '#92400e' : '#991b1b');
            @endphp
            <div style="background:{{ $bg }};color:{{ $fg }};border-radius:8px;
                        padding:10px 14px;text-align:center;min-width:80px;">
                <div style="font-size:.75rem;font-weight:600;">{{ \Carbon\Carbon::parse($ym.'-01')->format('M Y') }}</div>
                <div style="font-size:1.1rem;font-weight:700;margin-top:4px;">{{ $rate }}%</div>
            </div>
            @endforeach
        </div>
        <div class="d-flex gap-4 mt-3" style="font-size:.8rem;">
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#d1fae5;margin-right:5px;"></span>≥ 80%</span>
            <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#fef3c7;margin-right:5px;"></span>60–80%</span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('ChartJS/chart.umd.min.js') }}"></script>
<script>
// Trend chart
const trendLabels = @json($monthlyTrend->pluck('month_label'));
const gradeAvgs   = @json($monthlyTrend->pluck('avg_pct'));
const attAvgs     = @json($monthlyTrend->pluck('att_pct'));

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [
            { label: 'Avg Grade %', data: gradeAvgs, borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,.08)',
              fill: true, tension: .4, borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#4f46e5' },
            { label: 'Attendance %', data: attAvgs, borderColor: '#059669', backgroundColor: 'transparent',
              borderDash: [5,5], tension: .4, borderWidth: 2, pointRadius: 4, pointBackgroundColor: '#059669' }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { font: { size: 11 } } } },
        scales: { y: { beginAtZero: false, min: 40, grid: { color: '#f1f5f9' } },
                  x: { grid: { display: false } } }
    }
});

// Grade pie
const pieLabels = @json($gradeDistribution->keys());
const pieCounts = @json($gradeDistribution->values());
new Chart(document.getElementById('gradePie'), {
    type: 'doughnut',
    data: {
        labels: pieLabels,
        datasets: [{ data: pieCounts,
            backgroundColor: ['#6d28d9','#2563eb','#059669','#d97706','#9ca3af','#dc2626','#374151','#ef4444'],
            borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { display: false } } }
});

// Class performance horizontal bar
const classNames = @json($classPerformance->map(fn($c) => $c->name));
const classGrades= @json($classPerformance->pluck('avg_grade'));
new Chart(document.getElementById('classChart'), {
    type: 'bar',
    data: {
        labels: classNames,
        datasets: [{ label: 'Avg Grade %', data: classGrades,
            backgroundColor: classGrades.map(v => v>=80?'#d1fae5':v>=70?'#dbeafe':v>=60?'#fef3c7':'#fee2e2'),
            borderRadius: 6, borderSkipped: false }]
    },
    options: { indexAxis: 'y', responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' } },
                  y: { grid: { display: false } } } }
});

// Subject performance
const subNames  = @json($subjectPerformance->map(fn($s) => $s->name));
const subGrades = @json($subjectPerformance->pluck('avg_grade'));
new Chart(document.getElementById('subjectChart'), {
    type: 'bar',
    data: {
        labels: subNames,
        datasets: [{ label: 'Avg Grade %', data: subGrades,
            backgroundColor: '#dbeafe', borderColor: '#2563eb', borderWidth: 1,
            borderRadius: 6, borderSkipped: false }]
    },
    options: { indexAxis: 'y', responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' } },
                  y: { grid: { display: false } } } }
});
</script>
@endpush
